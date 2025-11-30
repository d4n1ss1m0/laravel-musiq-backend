<?php

namespace App\Infrastructure\Services\FileService;

use Illuminate\Support\Str;

class FileService
{
    public function addFile($file, $path): string
    {
        $fileType = $file->getClientOriginalExtension();
        $hash = Str::uuid()->toString();
        $path = $file->storeAs($path, "{$hash}.{$fileType}", 'local');
        return $path;
    }

    public function convertMusicFile($path): string
    {
        try {
            $hash = Str::uuid()->toString();
            $outputFile = storage_path('app/audio') . '/' . $hash . '.webm';
            // Составляем команду FFmpeg
            //putenv('PATH=/usr/bin:/usr/local/bin:' . getenv('PATH'));
            $command = sprintf(
                'ffmpeg -i %s -c:a libopus -b:a 128k -vbr on -ar 48000 %s',
                escapeshellarg($path),
                escapeshellarg($outputFile)
            );

            // Выполняем команду
            exec($command, $output, $returnCode);
            // Проверяем успешность выполнения команды
            if ($returnCode !== 0) {
                return response()->json([
                    'error' => 'Ошибка конвертации файла.',
                    'output' => $output,
                ], 500);
            }

            // Удаляем исходный файл (опционально)
            unlink($path);

            return $hash . '.webm';
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Произошла ошибка при конвертации файла.',
                'message' => $e->getMessage(),
            ], 500);
        }

    }
}
