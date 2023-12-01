<?php

namespace App\Domain\FileStorage;

use Illuminate\Http\UploadedFile;
use App\Domain\FileStorage\IFileStorageService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Throwable;

class S3FileStorageService implements IFileStorageService
{
    private string $bucketPath = 'ihs/images';
    /**
     * ファイルをS3にアップロード
     *
     * @param UploadedFile $file アップロードするファイル
     * @return void
     */
    public function uploadFile(UploadedFile $file)
    {
        try {
            if (app()->isLocal()) {
                // 開発環境
                $path = $file->store('public/files');
                return Storage::url($path);
            } else {
                // 本番環境
                $path = Storage::disk('s3')->putFile(
                    $this->bucketPath,
                    $file,
                    'public'
                );
                return Storage::disk('s3')->url($path);
            }
        } catch (Throwable $th) {
            Log::error($th);
            return '';
        }
    }
    /**
     * S3からファイルを削除
     *
     * @param string $filePath
     * @return boolean 実行結果
     */
    public function deleteFile(string $filePath): bool
    {
        if (app()->isLocal()) {
            // 開発環境
            $splitData = explode('storage', $filePath);
            $s3Delete = Storage::delete('public' . $splitData[1]);
            return $s3Delete;
        } else {
            // 本番環境
            $splitData = explode($this->bucketPath, $filePath);
            $s3Delete = Storage::disk('s3')->delete(
                $this->bucketPath . $splitData[1]
            );
            return $s3Delete;
        }
    }
    /**
     * ファイルを保持しているか(ファイルパスが保存されているか)
     *
     * @param string|null $filePath
     * @return boolean
     */
    public function hasFile(?string $filePath): bool
    {
        if (is_null($filePath)) {
            return false;
        }
        return $filePath != '';
    }
}
