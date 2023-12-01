<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/health.html', function () {
    return 'health check';
});
// ログイン画面
Route::get('/login', function () {
    return view('login');
});
// 認証前のリクエストパラメータをセッションに保存
Route::post('/beforeLogin', [LoginController::class, 'beforeLogin']);
// プッシュ通知用の端末トークンを削除
Route::post('/beforeLogout', [LoginController::class, 'beforeLogout']);
// 認証基盤からのリダイレクト
Route::get('/callback', [LoginController::class, 'callback']);
// 独自認証用
Route::post('/login', [LoginController::class, 'loggedIn'])
->name('login')
->middleware('signed');
Route::post('/logout', [LoginController::class, 'logout'])
->name('logout');

// 管理画面
Route::group(
    ['middleware' => ['ha.sso', 'ha.admin'], 'as' => 'internal.'],
    function () {
        // リダイレクト用
        Route::get('/admin', function () {
            return view('admin');
        });
        Route::get('/admin/account', function () {
            return view('admin');
        });
        Route::get('/admin/account/{any}', function () {
            return view('admin');
        });
        Route::get('/admin/in_house_system', function () {
            return view('admin');
        });
        Route::get('/admin/in_house_system/{any}', function () {
            return view('admin');
        });
        Route::get('/admin/report_form', function () {
            return view('admin');
        });
        Route::get('/admin/report_form/{any}', function () {
            return view('admin');
        });
        Route::get('/admin/company', function () {
            return view('admin');
        });
        Route::get('/admin/company/{any}', function () {
            return view('admin');
        });
        Route::get('/admin/organization', function () {
            return view('admin');
        });
        Route::get('/admin/organization/{any}', function () {
            return view('admin');
        });
    }
);
Route::group(
    ['middleware' => ['ha.sso', 'ha.employee'], 'as' => 'internal.'],
    function () {
        // リダイレクト用
        Route::get('/', function () {
            return view('index');
        });
        Route::get('/editreport/{any}', function () {
            return view('index');
        })->where('any', '[0-9]+');
        // SP用ルーティング
        Route::get('/sphome', function () {
            return view('sphome');
        });
        Route::get('/speditreport/{any}', function () {
            return view('sphome');
        });
        Route::get('/spsendreport', function () {
            return view('sphome');
        });

        Route::get('/{any}', function () {
            return view('index');
        })->where('any', '(home|sendreport)');
    }
);

if (env('APP_DEBUG', false)) {
    Route::get('/phpinfo', function () {
        phpinfo();
    });
}
