<?php

namespace App\Providers;

use App\Domain\Human\HumanServic;
use Exception;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Domain\Human\IHumanService;
use App\Domain\Human\InMemoryHumanService;
use App\Domain\Human\HumanService;
use App\Domain\FileStorage\IFileStorageService;
use App\Domain\FileStorage\S3FileStorageService;
use App\Domain\StatisticsCsv\IStatisticsCsvService;
use App\Domain\StatisticsCsv\StatisticsCsvService;
use App\Domain\MoviePlayer\IMoviePlayerService;
use App\Domain\MoviePlayer\UlizaPlayerService;
use App\Domain\PushNotification\IPushNotificationService;
use App\Domain\PushNotification\AwsPinpointService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // ヒューマンサービス
        $this->app->singleton(IHumanService::class, function ($app) {
            if (config('app.env') == 'local') {
                return new InMemoryHumanService();
            } else {
                return new HumanService();
            }
        });
        // ファイルストレージ
        $this->app->singleton(IFileStorageService::class, function ($app) {
            return new S3FileStorageService();
        });
        // 統計CSV出力
        $this->app->singleton(IStatisticsCsvService::class, function ($app) {
            return new StatisticsCsvService();
        });
        // 動画サービス
        $this->app->singleton(IMoviePlayerService::class, function ($app) {
            return new UlizaPlayerService();
        });
        // プッシュ通知サービス
        $this->app->singleton(IPushNotificationService::class, function ($app) {
            return new AwsPinpointService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ヒューマンID取得メソッドを追加
        $this->addMacroRequestGetHumanId();

        // 管理者ID取得メソッドを追加
        $this->addMacroRequestGetAdminUserId();

        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
        } else {
            URL::forceScheme('http');
            $this->app['request']->server->set('HTTP', 'on');
        }
    }

    private function addMacroRequestGetHumanId()
    {
        /**
         * CookieからヒューマンIDを取得
         */
        Request::macro('getHumanObject', function () {
            try {
                $originSession = request()->session()->get('timeline_origin_session');
                if (!empty($originSession)) {
                    // 認証基盤を使わず独自にログインしている
                    return (object)$originSession;
                }
        
                // SSO環境判定
                if (config('app.ha_sso')) {
                    // 本番用
                    if (Cookie::has('_edu_data')) {
                        // cookieの解析
                        $method = 'AES-256-CBC';
                        $key = base64_decode(config('app.ha_sso_key'));
                        $data = json_decode(
                            base64_decode(urldecode(Cookie::get('_edu_data')))
                        );
                        if (
                            !is_array($data) ||
                            empty($data[0]) ||
                            empty($data[1]) ||
                            empty($data[2])
                        ) {
                            throw new Exception('cookie data invalid.');
                        }
                        $encrypted = base64_decode($data[0]);
                        $iv = base64_decode($data[1]);
                        $mac = base64_decode($data[2]);
                        if (
                            !hash_equals(
                                hash_hmac('sha256', $iv . $encrypted, $key),
                                $mac
                            )
                        ) {
                            throw new Exception('cookie data invalid.');
                        }
                        $decrypted = openssl_decrypt(
                            $encrypted,
                            $method,
                            $key,
                            OPENSSL_RAW_DATA,
                            $iv
                        );
                        if (!$decrypted) {
                            throw new Exception('decrypt failed.');
                        }
                        $decryptedCookie = json_decode($decrypted);
                        return $decryptedCookie;
                    }
                } else {
                    // ローカル用
                    if (Cookie::has('_edu_data_time')) {
                        $data = json_decode(Cookie::get('_edu_data_time'));
                        $humanObj = json_decode($data[0]);
                        return $humanObj;
                    }
                }
                return false;
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return false;
            }
        });
    }

    private function addMacroRequestGetAdminUserId()
    {
        /**
         * 管理者セッションからユーザIDを取得
         */
        Request::macro('getAdminUserId', function ($request) {
            if ($request->session()->has('ha_lms_admin_session')) {
                return $request->session()->get('ha_lms_admin_session.id');
            }
            // セッションが取得できない
            // 未ログインとみなす
            abort(401);
        });
    }
}
