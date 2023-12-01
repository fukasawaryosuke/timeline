<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class HaEmployeeAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $originSession = request()->session()->get('timeline_origin_session');
        if (!empty($originSession)) {
            // 認証基盤を使わず独自にログインしている
            return $next($request);
        }

        // SSO環境判定
        if (config('app.ha_sso')) {
            // 管理者セッションがあるか
            $sessionAdmin = request()->session()->get('ha_lms_admin_session');
            $sessionUser = request()->session()->get('ha_time_session');

            $client = new \GuzzleHttp\Client([
                'headers' => ['Content-Type' => 'application/json', 'x-apigw-api-id' => config('app.db_api_key')],
            ]);
            $res = $client->get(
                config('app.db_api_base').'/api/core/get-teacher-employee-profile?teacher_employee_id=' . $sessionUser['teacher_employee_id'],
            );
            $divisionJson = json_decode($res->getBody(), true);
            Log::debug(print_r($divisionJson, true));

            if (empty($sessionAdmin)
            || $sessionAdmin['human_id'] != $sessionUser['human_id']) {
                // 管理者セッションが存在しない
                // または一般セッションのヒューマンIDと管理者セッションのヒューマンIDが一致しない
                // 一般セッションから会員種別を判定
                $isEmployee = array_search('mk_emp', array_column($sessionUser['member_kinds'], 'member_kind'));
                if ($isEmployee === false) {
                    // 社員ではない
                    $this->notEnployeeError($sessionUser);
                }

                // 管理者テーブルに社員が存在しているか
                $user = User::where('human_id', $sessionUser['human_id'])->first();
                if (is_null($user)) {
                    // 管理者テーブルに存在しない社員
                    // 社員は管理者権限でアカウントを作成する
                    $user = User::create([
                        'human_id' => $sessionUser['human_id'],
                        'system_role_id' => 1,
                        'user_name' => $sessionUser['name'],
                        'mailadd' => $sessionUser['mailadd'],
                        'division_cd' => $divisionJson['部署CD']
                    ]);
                }

                // 認証基盤と情報を同期する
                if ($user->user_name != $sessionUser['name']
                || $user->mailadd != $sessionUser['mailadd']
                || (!empty($divisionJson['部署CD']) && $user->division_cd != $divisionJson['部署CD'])) {
                    $user->user_name = $sessionUser['name'];
                    $user->mailadd = $sessionUser['mailadd'];
                    $user->division_cd = $divisionJson['部署CD'];
                    $user->save();
                }

                // セッションを作る
                request()->session()->put('ha_lms_admin_session', [
                    'id' => $user->id,
                    'human_id' => $user->human_id,
                    'system_role_id' => $user->system_role_id,
                    'name' => $user->user_name,
                    'mailadd' => $user->mailadd,
                    'division_cd' => $user->division_cd,
                ]);
            }
        } else {
            // 管理者セッションを作る
            $sessionUser = request()->session()->get('ha_time_session');

            // 管理者テーブルに社員が存在しているか
            $user = User::where('human_id', $sessionUser['human_id'])->first();
            if (is_null($user)) {
                // 管理者テーブルに存在しない社員
                $user = User::create([
                    'human_id' => $sessionUser['human_id'],
                    'system_role_id' => 1,
                    'user_name' => $sessionUser['name'],
                    'mailadd' => $sessionUser['mailadd'],
                    'division_cd' => 19924,
                    'company_code' => '0001',
                ]);
            }

            request()->session()->put('ha_lms_admin_session', [
                'id' => $user->id,
                'human_id' => $user->human_id,
                'system_role_id' => $user->system_role_id,
                'name' => $user->user_name,
                'mailadd' => $user->mailadd,
                'division_cd' => $user->division_cd,
                'company_code' => $user->company_code,
            ]);
        }
        return $next($request);
    }

    private function notOrganizationError($sessionUser, $divisionCd)
    {
        Log::warning('Session user organization not found.');
        Log::warning('Session human_id: '.$sessionUser['human_id']);
        Log::warning('Session mail: '.$sessionUser['mailadd']);
        Log::warning('Session member_kinds: '.implode(', ', array_column($sessionUser['member_kinds'], 'member_kind')));
        Log::warning('Session division_code: '.$divisionCd);
        $this->adminAuthAbort(403);
    }


    private function notEnployeeError($sessionUser)
    {
        Log::warning('Session user is not employee.');
        Log::warning('Session human_id: '.$sessionUser['human_id']);
        Log::warning('Session mail: '.$sessionUser['mailadd']);
        Log::warning('Session member_kinds: '.implode(', ', array_column($sessionUser['member_kinds'], 'member_kind')));
        $this->adminAuthAbort(403);
    }

    private function userNotFoundError($sessionUser)
    {
        Log::warning('Not found in admin_user.');
        Log::warning('Session human_id: '.$sessionUser['human_id']);
        Log::warning('Session mail: '.$sessionUser['mailadd']);
        Log::warning('Created admin_user with unallocated role.');
        $this->adminAuthAbort(403);
    }

    private function userUnallocatedError($sessionUser)
    {
        Log::warning('Unallocated in admin_user.');
        Log::warning('Session human_id: '.$sessionUser['human_id']);
        Log::warning('Session mail: '.$sessionUser['mailadd']);
        $this->adminAuthAbort(403);
    }

    private function adminAuthAbort($status)
    {
        // セッションを破棄
        request()->session()->forget('ha_lms_admin_session');

        // 画面のリクエストかAPIリクエストか
        if (Route::is('api.*')) {
            $error = [
                'code' => 999,
                'error' => 'SSO Error occurred',
            ];
            if ($status == 401) {
                $error = [
                    'code' => 4,
                    'error' => 'SSO Unauthorized',
                ];
            } elseif ($status == 403) {
                $error = [
                    'code' => 4,
                    'error' => 'SSO Unauthorized',
                ];
            }
            // エラーJSONを返却
            abort(response()->json($error, $status));
        } else {
            // エラー画面を返却
            abort($status);
        }
    }
}
