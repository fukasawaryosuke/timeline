<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Device;
use Illuminate\Support\Facades\URL;

class LoginController extends Controller
{
    // 認証基盤からのリダイレクト
    // エラーコードについては認証基盤のドキュメント参照
    public function callback(Request $request)
    {
        $errorCode = request()->input('error_code');
        if ($errorCode) {
            $id = $request->session()->get('loginId', 'not exsist id');
            $password = $request->session()->get('loginPassword', '');
            Log::warning(
                "message: Login Error,id: {$id}, error_code: {$errorCode}"
            );
            $request->session()->forget('login');

            $companyCode = $request->route('companyCode');
            $loginUrl = "/login";
            if (!empty($companyCode)) {
                $loginUrl = "/login/{$companyCode}";
            }
    
            switch ($errorCode) {
                case '1000': // 必須パラメータエラー
                    $id = urlencode($id);
                    $password = urlencode($password);
                    $message = urlencode('認証に失敗しました');
                    return redirect(
                        $loginUrl."?error_message={$message}&id={$id}&password={$password}"
                    );
                    break;
                case '2015': // 対象サービスがマスタに未登録
                    $id = urlencode($id);
                    $password = urlencode($password);
                    $message = urlencode('認証に失敗しました');
                    return redirect(
                        $loginUrl."?error_message={$message}&id={$id}&password={$password}"
                    );
                    break;
                case '1004': // サーバ内部エラー
                    $id = urlencode($id);
                    $password = urlencode($password);
                    $message = urlencode('認証に失敗しました');
                    return redirect(
                        $loginUrl."?error_message={$message}&id={$id}&password={$password}"
                    );
                    break;
                case '2009': // 対象会員未存在
                    $id = urlencode($id);
                    $password = urlencode($password);
                    $message = urlencode('認証に失敗しました');
                    return redirect(
                        $loginUrl."?error_message={$message}&id={$id}&password={$password}"
                    );
                    break;
                case '2014': // 対象会員は仮会員
                    $id = urlencode($id);
                    $password = urlencode($password);
                    $message = urlencode('認証に失敗しました');
                    return redirect(
                        $loginUrl."?error_message={$message}&id={$id}&password={$password}"
                    );
                    break;
                default:
                    $id = urlencode($id);
                    $password = urlencode($password);
                    $message = urlencode('認証に失敗しました');
                    return redirect(
                        $loginUrl."?error_message={$message}&id={$id}&password={$password}"
                    );
                    break;
            }
        } else {
            $redirectHome = $request->session()->get('redirectHome', '');
            return redirect("/{$redirectHome}");
        }
    }
    // 認証基盤のレスポンスがエラーコードしか返却されないため、事前にリクエストパラメータをセッションに保存する処理
    public function beforeLogin(Request $request)
    {
        request()->session()->forget('timeline_origin_session');

        $id = request()->input('id');
        $password = request()->input('password');
        $defaultHome = request()->input('defaultHome');
        $companyCode = request()->input('companyCode');
        $request->session()->put('loginId', $id);
        $request->session()->put('loginPassword', $password);
        $request->session()->put('redirectHome', $defaultHome);
        $request->session()->put('companyCode', $companyCode);

        $ssoUrl = '';
        $company = Company::where('company_code', $companyCode)->first();
        if ($company) {
            if ($company->use_auth_platform) {
                if (config('app.ha_sso')) {
                    $ssoUrl = config('app.ha_sso_login_url');
                } else {
                    $ssoUrl = URL::signedRoute('login');
                }
            } else {
                $ssoUrl = URL::signedRoute('login');
            }
            return $this->successJsonResponse(200, '', ['ssoUrl' => $ssoUrl]);
        } else {
            return $this->successJsonResponse(404, '法人コードに該当する企業が見つかりませんでした。', ['ssoUrl' => $ssoUrl]);
        }
    }
    // 認証基盤を使わず独自にログイン
    public function loggedIn(Request $request)
    {
        $request->validate([
            'login_id' => 'required',
            'password' => 'required',
        ]);

        $loginId = request()->input('login_id');
        $password = request()->input('password');
        $companyCode = request()->input('company_code');

        $errorCode = '';
        $user = User::with(['company'])->where([
            'mailadd' => $loginId,
            'company_code' => $companyCode,
            'is_active' => 1
        ])->first();

        $callbackUrl = '/callback';
        if (strcmp(config('app.human_academy_company_code'), $companyCode) != 0) {
            $callbackUrl = "/callback/{$companyCode}";
        }

        if (empty($user)) {
            // ユーザが存在しない
            return redirect($callbackUrl."?error_code=2009");
        }

        if (strcmp($user->password, $password) != 0) {
            // パスワード不一致
            return redirect($callbackUrl."?error_code=2009");
        }

        if ($user->company->is_active != 1) {
            // 法人が無効
            return redirect($callbackUrl."?error_code=2009");
        }

        // セッション開始
        request()->session()->put('timeline_origin_session', [
            'human_id' => $user->human_id,
            'name' => $user->name,
            'mailadd' => $user->mailadd,
            'company_code' => $user->company_code,
            'use_auth_platform' => $user->company->use_auth_platform,
        ]);
        request()->session()->put('ha_time_session', [
            'human_id' => $user->human_id,
            'name' => $user->name,
            'mailadd' => $user->mailadd,
        ]);

        return redirect($callbackUrl."?error_code={$errorCode}");
    }
    // NOTE: ローカル用認証基盤ログアウト処理
    public function logout(Request $request)
    {
        $request->validate([
            'service_id' => 'required',
            'redirect_url' => 'required',
        ]);
        return redirect('/login');
    }
    // プッシュ通知用のデバイスのトークンを削除
    public function beforeLogout(Request $request)
    {
        $humanId = request()->getHumanObject()->human_id;
        $user = User::where('human_id', $humanId)->first();
        // ユーザーの端末のトークン有効期限をリセット
        if ($user != null) {
            Device::where('user_id', $user->id)->update(['expire' => null]);
        }
        return $this->successJsonResponse();
    }
}
