<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domain\MoviePlayer\IMoviePlayerService;
use App\Domain\FileStorage\IFileStorageService;
use Illuminate\Support\Facades\Log;

class MediaController extends Controller
{
    /**
     * 画像をアップロード
     *
     * @param IFileStorageService $service
     * @return void
     */
    public function uploadImage(IFileStorageService $service)
    {
        $file = request()->file('file');
        if (empty($file)) {
            return $this->errorJsonResponse(500, 'file is not found');
        }
        $result = $service->uploadFile($file);
        if ($result == '') {
            $this->errorJsonResponse(
                500,
                'ファイルのアップロードに失敗しました。'
            );
        }
        return $service->uploadFile($file);
    }
    /**
     * アップロード済みの画像を削除
     *
     * @param IFileStorageService $service
     * @return void
     */
    public function deleteImage(IFileStorageService $service)
    {
        $list = request()->input('list');
        if (empty($list)) {
            return $this->errorJsonResponse(500, 'list is not found');
        }
        foreach ($list as $l) {
            $service->deleteFile($l);
        }
        return $this->successJsonResponse();
    }
    /**
     * ulizaのjavascriptソースを取得
     *
     * @param Request $request
     * @param IMoviePlayerService $service
     * @return void
     */
    public function getMovieScript(
        Request $request,
        IMoviePlayerService $service
    ) {
        $contentName = $request->content_name;
        return $service->get($contentName);
    }
    /**
     * アップロードした動画が公開状況か確認
     *
     * @param Request $request
     * @param IMoviePlayerService $service
     * @return void
     */
    public function getMovieStatus(
        Request $request,
        IMoviePlayerService $service
    ) {
        $contentName = $request->content_name;
        $result = $service->getStatus($contentName);

        if (isset($result->code) || isset($result->message)) {
            return $this->errorJsonResponse($result->code, $result->message);
        }
        $status = $result->status;
        if ($status != IMoviePlayerService::STATUS_PUBLIC) {
            return $this->errorJsonResponse(
                404,
                'エンコード処理が終了していないため表示できません。'
            );
        }
        return $this->successJsonResponse();
    }
    /**
     * 動画をアップロード
     *
     * @param Request $request
     * @param IMoviePlayerService $service
     * @return void
     */
    public function uploadMovie(Request $request, IMoviePlayerService $service)
    {
        $file = request()->file('file');
        if (empty($file)) {
            return $this->errorJsonResponse(500, 'file is not found');
        }
        $result = $service->upload($request);

        if (isset($result->code) || isset($result->message)) {
            return $this->errorJsonResponse($result->code, $result->message);
        }
        return $result->transaction->content_name;
    }
    /**
     * アップロード済みの動画を削除
     *
     * @param IMoviePlayerService $service
     * @return void
     */
    public function deleteMovie(IMoviePlayerService $service)
    {
        $list = request()->input('list');
        if (empty($list)) {
            return $this->errorJsonResponse(500, 'list is not found');
        }
        foreach ($list as $l) {
            $service->delete($l);
        }
        return $this->successJsonResponse();
    }
}
