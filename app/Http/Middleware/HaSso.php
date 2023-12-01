<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

class HaSso
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
            // SSOクッキーからヒューマンオブジェクトを取得
            $humanObject = request()->getHumanObject();

            // SSOクッキー解析
            if ($humanObject === false) {
                // クッキーが取得できていない
                $this->cookieNotFoundError();
            }

            // トークン有効期限チェック
            if ($humanObject->expire == 'expire') {
                // クッキーに何らかの問題が発生している
                $this->cookieParseError($humanObject);
            }
            if ($humanObject->expire < time()) {
                // クッキーに何らかの問題が発生している
                $this->cookieParseError($humanObject);
            }

            // APIアクセストークンの有無チェック
            if (empty($humanObject->token)) {
                // クッキーに何らかの問題が発生している
                $this->cookieParseError($humanObject);
            }

            // lms内セッションとクッキー情報を比較
            $sessionUser = request()->session()->get('ha_time_session');
            if (empty($sessionUser)
            || $sessionUser['human_id'] != $humanObject->human_id) {
                // セッションにヒューマンIDがない場合、もしくはcookieと異なる場合
                // ユーザ情報取得APIを呼び出し
                list($res, $status) = $this->authPfMemberInfo($humanObject);

                if ($res) {
                    if ($res['status'] == 1) {
                        // レスポンスをセッションに保存
                        request()->session()->put('ha_time_session', $res['member_info']);
                    } else {
                        // 認証基盤のAPIがユーザ情報を返却していない
                        $this->authPfNoUserError($humanObject, $res);
                    }
                } else {
                    // 認証基盤を正常に叩けなかった
                    // 認証基盤のステータスコードを返却する
                    $this->authPfApiError($humanObject, $status);
                }
            }
        } else {
            $humanObject = false;
            // ローカル用
            if (!Cookie::has('_edu_data_time')) {
                $humanData = json_encode([
                    'human_id' => Str::uuid(),
                    'token' => 'TOKEN dammy',
                    'expire' => time() + (3600 * 24 * 3),
                ]);
                // cookieを作る
                Cookie::queue('_edu_data_time', json_encode([
                    $humanData,
                    'IV dammy',
                    'HMAC dammy'
                ]), 60 * 24 * 3);
                $humanObject = json_decode($humanData);
            } else {
                // cookieがある
                $humanObject = request()->getHumanObject();
            }
            if ($humanObject === false) {
                // クッキーが取得できていない
                $this->cookieNotFoundError();
            }

            // セッションに保存
            request()->session()->put('ha_time_session', [
                'human_id' => $humanObject->human_id,
                'name' => '確認用(自動生成)' . Str::random(7),
                'mailadd' => 'test@test.test',
            ]);
        }
        return $next($request);
    }

    private function cookieNotFoundError() {
        Log::warning('SSO cookie not found.');
        $this->ssoAbort(401);
    }

    private function cookieParseError($humanObject) {
        Log::warning('SSO cookie parse error.');
        Log::warning('Cookie human_id: '.$humanObject->human_id);
        Log::warning('Cookie token: '.$humanObject->token);
        Log::warning('Cookie expire: '.$humanObject->expire);
        // クッキーに何らかの問題が発生しているので未ログイン状態とする
        $this->ssoAbort(401);
    }

    private function authPfApiError($humanObject, $apiStatus) {
        Log::warning('Auth platform returns error.');
        Log::warning('Cookie human_id: '.$humanObject->human_id);
        Log::warning('Cookie token: '.$humanObject->token);
        Log::warning('Cookie expire: '.$humanObject->expire);
        Log::warning('Auth API status: '.$apiStatus);
        $this->ssoAbort($apiStatus);
    }

    private function authPfNoUserError($humanObject, $apiRes) {
        Log::warning('Auth platform returns no member.');
        Log::warning('Cookie human_id: '.$humanObject->human_id);
        Log::warning('Cookie token: '.$humanObject->token);
        Log::warning('Cookie expire: '.$humanObject->expire);
        Log::warning('Auth API returns: '.print_r($apiRes, true));
        $this->ssoAbort(500);
    }

    private function ssoAbort($status) {
        // SSOクッキーを破棄
        $this->removeSsoCookie();

        // セッションを破棄
        request()->session()->forget('ha_time_session');

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
            } else if($status == 403) {
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

    private function removeSsoCookie() {
        $cookieDomain = null;
        $cookieSecure = null;
        if (config('app.ha_sso') === true) {
            $cookieDomain = '.athuman.com';
            $cookieSecure = true;
        }
        // SSOクッキーをセット
        $ssoCookie = Cookie::make(
            '_edu_data',
            null,
            Carbon::now()->diffInMinutes(Carbon::now()->subYear()),
            '/',
            $cookieDomain,
            $cookieSecure
        );
        Cookie::queue($ssoCookie);
    }

    private function authPfMemberInfo($humanObject) {
        return $this->authPfGetRequest(
            config('app.ha_auth_pf_url').'/member_info/'.$humanObject->human_id,
            [],
            $humanObject->token
        );
    }

    private function authPfGetRequest($url, $params, $token) {
        if (!empty($params)) {
            $url = $url.'?'.http_build_query($params);
        }

        $res = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->get($url);

        if ($res->ok()) {
            return [$res->json(), $res->status()];
        }
        // httpステータスが成功していない
        return [false, $res->status()];
    }
}
