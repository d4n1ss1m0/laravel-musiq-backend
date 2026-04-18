<?php

namespace App\Service\FileService;

use Illuminate\Support\Str;

class FileService implements FileServiceInterface
{
    public function addFile($file, $path, string|null $extension = null): string
    {
        if ($extension === null) {
            $fileType = $file->getClientOriginalExtension();
        } else {
            $fileType = $extension;
        }

        $hash = Str::uuid()->toString();
        $fileName = "{$hash}.{$fileType}";
        $path = $file->storeAs($path, $fileName, 'local');
        return $fileName;
    }

    public function convertMusicFile($path): string
    {
        try {
            $hash = Str::uuid()->toString();
            $outputFile = storage_path('app/audio') . '/' . $hash . '.ogg';

            $command = sprintf(
                'ffmpeg -i %s -c:a libvorbis -q:a 5 -ar 48000 %s 2>&1',
                escapeshellarg($path),
                escapeshellarg($outputFile)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \RuntimeException('FFmpeg error: ' . implode("\n", $output));
            }

            unlink($path);

            return $hash . '.ogg';
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
