<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DairyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DraftsController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ReportFormController;
use App\Http\Controllers\InHouseSystemController;
use App\Http\Controllers\MustReadController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\WebPushController;
use App\Http\Controllers\CompanyController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['ha.sso'], 'as' => 'internal.'], function () {
    Route::prefix('/diary')->group(function () {
        Route::get('/inHouseSystem', [DairyController::class, 'inHouseSystem']);
        Route::get('/csvDownload', [DairyController::class, 'csvDownload']);
        Route::get('/showDairy', [DairyController::class, 'show']);
        Route::get('/diaryDetail', [DairyController::class, 'detail']);
        Route::get('/myDiary', [DairyController::class, 'myDiary']);
        Route::post('/createS3image', [
            DairyController::class,
            'createS3image',
        ]);
        Route::delete('/deleteDiary', [DairyController::class, 'delete']);
        Route::post('/newDairyCreate/{ihs_id}', [
            DairyController::class,
            'newDairyCreate',
        ])->where('ihs_id', '[0-9]+');
        Route::get('/latestDairy/{ihs_id}', [
            DairyController::class,
            'latest',
        ])->where('ihs_id', '[0-9]+');
        Route::put('/updateDiary/{diary_id}', [
            DairyController::class,
            'update',
        ])->where('diary_id', '[0-9]+');
    });

    Route::prefix('/users')->group(function () {
        Route::get('/loginuser', [UserController::class, 'show']);
        Route::get('/loginuser/isHumanAcademy', [UserController::class, 'isHumanAcademy']);
        Route::get('/mention/user', [UserController::class, 'mentionUser']);
        Route::get('/getEmployeeProfile', [
            UserController::class,
            'getEmployeeProfile',
        ]);
        Route::get('/divisions', [UserController::class, 'divisions']);
        Route::get('/getProfileIcon/{humanId}', [UserController::class, 'getProfileIcon']);
        Route::get('/user_name', [UserController::class, 'getUserName']);
    });

    Route::prefix('/account')->group(function () {
        Route::middleware('ha.admin')->group(function () {
            Route::get('/list', [UserController::class, 'list']);
            Route::get('/userlist', [UserController::class, 'userlist']);
            Route::put('/permissoin/{user_id}', [
                UserController::class,
                'updatePermission',
            ])->where('user_id', '[0-9]+');
            Route::post('/create', [UserController::class, 'create']);
            Route::get('/{user_id}', [UserController::class, 'detail']);
            Route::put('/update', [UserController::class, 'update']);
            Route::put('/update/password', [UserController::class, 'updatePassword']);
            Route::put('/reset/password', [UserController::class, 'resetPassword']);
        });
    });

    Route::prefix('/comment')->group(function () {
        Route::post('/create', [CommentController::class, 'create']);
        Route::get('/show', [CommentController::class, 'show']);
        Route::get('/showNotice', [CommentController::class, 'showNotice']);
        Route::get('/csvDownload', [CommentController::class, 'csvDownload']);
    });

    Route::prefix('/history')->group(function () {
        Route::post('/registHistory', [HistoryController::class, 'regist']);
        Route::get('/showHistory', [HistoryController::class, 'show']);
        Route::get('/spShowHistory', [HistoryController::class, 'spShow']);
    });

    Route::prefix('/ihs')->group(function () {
        Route::get('/show', [InHouseSystemController::class, 'show']);
        Route::get('/all', [InHouseSystemController::class, 'all']);
        Route::put('/sort', [InHouseSystemController::class, 'sort']);
        Route::get('/list', [InHouseSystemController::class, 'selectList']);
        Route::get('/{ihs_id}', [
            InHouseSystemController::class,
            'detail',
        ])->where('ihs_id', '[0-9]+');

        Route::middleware('ha.admin')->group(function () {
            Route::post('/create', [InHouseSystemController::class, 'create']);
            Route::put('/{ihs_id}', [
                InHouseSystemController::class,
                'update',
            ])->where('ihs_id', '[0-9]+');
            Route::delete('/{ihs_id}', [
                InHouseSystemController::class,
                'delete',
            ])->where('ihs_id', '[0-9]+');
        });
    });

    Route::prefix('/reportForm')->group(function () {
        Route::get('/show', [ReportFormController::class, 'show']);
        Route::get('/getForm/{ihs_id}', [
            ReportFormController::class,
            'get_form',
        ])->where('ihs_id', '[0-9]+');

        Route::middleware('ha.admin')->group(function () {
            Route::get('/{format_id}', [
                ReportFormController::class,
                'detail',
            ])->where('format_id', '[0-9]+');
            Route::get('/getFormList', [
                ReportFormController::class,
                'get_form_list',
            ]);
            Route::post('/create', [ReportFormController::class, 'create']);
            Route::put('/{format_id}', [
                ReportFormController::class,
                'update',
            ])->where('format_id', '[0-9]+');
            Route::delete('/{format_id}', [
                ReportFormController::class,
                'delete',
            ])->where('format_id', '[0-9]+');
        });
    });

    Route::prefix('/company')->group(function () {
        Route::get('/list', [CompanyController::class, 'list']);
        Route::get('/{company_id}', [CompanyController::class, 'detail']);
        Route::post('/create', [CompanyController::class, 'create']);
        Route::put('/update', [CompanyController::class, 'update']);
    });

    Route::prefix('/organization')->group(function () {
        Route::get('/list', [OrganizationController::class, 'list']);
        Route::get('/{organization_id}', [OrganizationController::class, 'detail']);
        Route::post('/create', [OrganizationController::class, 'create']);
        Route::put('/update', [OrganizationController::class, 'update']);
    });

    Route::get('/organization', [OrganizationController::class, 'show']);
    Route::get('/organization_div', [OrganizationController::class, 'getDiv']);
    Route::get('/organization_name', [OrganizationController::class, 'mappingName']);

    Route::prefix('/media')->group(function () {
        Route::get('/movie/status/{content_name}', [
            MediaController::class,
            'getMovieStatus',
        ]);

        Route::get('/movie/script/{content_name}', [
            MediaController::class,
            'getMovieScript',
        ]);
        Route::post('/movie/upload', [MediaController::class, 'uploadMovie']);
        Route::post('/movie/delete', [MediaController::class, 'deleteMovie']);
        Route::post('/image/upload', [MediaController::class, 'uploadImage']);
        Route::post('/image/delete', [MediaController::class, 'deleteImage']);
    });

    Route::get('/must/showNotice', [MustReadController::class, 'showNotice']);

    Route::prefix('/push')->group(function () {
        Route::post('/device', [WebPushController::class, 'createDevice']);
        Route::put('/device/{device_id}', [
            WebPushController::class,
            'updateDevice',
        ])->where('device_id', '[0-9]+');
        Route::put('/updateEndpoint', [
            WebPushController::class,
            'updateEndpoint',
        ]);
        // TODO: テスト用なので後で削除
        Route::get('/push', [WebPushController::class, 'pushNotification']);
        Route::get('/events', [WebPushController::class, 'putEvents']);
        Route::get('/get', [WebPushController::class, 'getUserEndpoint']);
    });

    Route::get('/ssohealth', function () {
        return response()->json(['message' => 'ok'], 200);
    });
});
Route::prefix('/drafts')->group(function () {
    Route::post('/create/{ihs_id}', [DraftsController::class, 'create']);
});

/**
 * apache > php > mysqlの同通を確認するためのAPI
 */
Route::get('/check', function () {
    $res = Illuminate\Support\Facades\DB::select('SELECT 1 AS `check`');
    return response()->json($res[0]);
});
