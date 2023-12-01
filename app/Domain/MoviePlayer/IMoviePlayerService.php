<?php

namespace App\Domain\MoviePlayer;

use Illuminate\Http\Request;

interface IMoviePlayerService
{
    public const STATUS_PUBLIC = 'PUBLIC';
    public function get(string $contentName);
    public function getStatus(string $contentName);
    public function getSream(Request $request);
    public function upload(Request $request);
    public function uploadStream(Request $request);
    public function delete(string $contentName);
}
