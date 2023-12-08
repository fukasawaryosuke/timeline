<?php

namespace App\Domain\FileStorage;

use Illuminate\Http\UploadedFile;

interface IFileStorageService
{
    public function uploadFile(UploadedFile $file);
    public function deleteFile(string $filePath): bool;
    public function hasFile(string $filePath): bool;
    public function getFile(string $filePath): string;
}
