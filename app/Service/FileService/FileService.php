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
            $outputFile = storage_path('app/audio') . '/' . $hash . '.opus';

            $command = sprintf(
                'ffmpeg -i %s -c:a libopus -b:a 96k -vbr on -ar 48000 -application audio %s 2>&1',
                escapeshellarg($path),
                escapeshellarg($outputFile)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \RuntimeException('FFmpeg error: ' . implode("\n", $output));
            }

            unlink($path);

            return $hash . '.opus';
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
