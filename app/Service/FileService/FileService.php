<?php

namespace App\Service\FileService;

use App\Service\Auth\AccountService;
use App\Shared\Traits\HttpResponse;
use FFMpeg\FFMpeg;
use FFMpeg\Media\Audio;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FileService
{
    use HttpResponse;
    public function __construct(private readonly AccountService $accountService)
    {
    }


    public function addFile($file, $path): string
    {
        $fileType = $file->getClientOriginalExtension();
        $hash = Str::uuid()->toString();
        $path = $file->storeAs($path, "{$hash}.{$fileType}", 'local');
        return $path;
    }

    public function convertMusicFile(string $path): string
    {
        $hash = Str::uuid()->toString();
        $outputFile = storage_path("app/audio/{$hash}.mp3");

        $command = sprintf(
            'ffmpeg -i %s -vn -ar 44100 -ac 2 -b:a 192k -codec:a libmp3lame %s 2>&1',
            escapeshellarg($path),
            escapeshellarg($outputFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::error('FFmpeg conversion failed', [
                'command' => $command,
                'output' => $output
            ]);
            throw new \RuntimeException('Ошибка конвертации аудио');
        }

        unlink($path);

        return "audio/{$hash}.mp3";
    }

    public function deleteFile($path) {

    }
}
