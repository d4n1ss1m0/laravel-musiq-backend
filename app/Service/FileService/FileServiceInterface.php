<?php

namespace App\Service\FileService;

use Illuminate\Http\UploadedFile;

interface FileServiceInterface
{
    public function addFile(UploadedFile $file, string $path, string|null $extension = null): string;
    public function convertMusicFile(string $path): string;
    public function moveFile(string $from, string $to): void;
}
