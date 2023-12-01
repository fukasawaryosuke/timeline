<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 共通の成功レスポンス
     * 使用例:
     * return $this->errorJsonResponse(200, $data, 'success');
     *
     * @param integer $code ステータスコード
     * @param string $message メッセージ
     * @param mixed $data 返却したいデータ
     * @return void
     */
    protected function successJsonResponse(
        $code = 200,
        $message = '',
        $data = null
    ) {
        // Log::info($message); // 必要に応じてコメントアウトを解除する
        return response()->json(
            ['data' => $data, 'message' => $message],
            $code
        );
    }
    /**
     * 共通のエラーレスポンス
     * 使用例:
     * return $this->errorJsonResponse(404, $data, 'not found');
     *
     * @param integer $code ステータスコード
     * @param string $message メッセージ
     * @return void $data 返却したいデータ
     */
    protected function errorJsonResponse(
        $code = 500,
        $message = '',
        $data = null
    ) {
        Log::error($message);
        return response()->json(
            ['data' => $data, 'message' => $message],
            $code
        );
    }
}
