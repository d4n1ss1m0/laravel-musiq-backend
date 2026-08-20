<?php

namespace App\Service\FileService;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

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
                'ffmpeg -i %s -map 0:a:0 -vn -c:a libvorbis -q:a 5 -ar 48000 %s 2>&1',
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

    /**
     * @return array{integrated_lufs: float, true_peak_db: float}
     */
    public function analyzeMusicFile(string $path, int $timeout = 600): array
    {
        if (!is_file($path)) {
            throw new \RuntimeException("Audio file not found: {$path}");
        }

        $process = new Process([
            'ffmpeg',
            '-hide_banner',
            '-nostats',
            '-i',
            $path,
            '-af',
            'loudnorm=I=-16:TP=-1.5:LRA=11:print_format=json',
            '-f',
            'null',
            '-',
        ]);
        $process->setTimeout($timeout);
        $process->run();

        $output = $process->getErrorOutput() . "\n" . $process->getOutput();

        if (!preg_match('/\{[\s\S]*"input_i"[\s\S]*\}/', $output, $matches)) {
            throw new \RuntimeException('Could not parse FFmpeg loudnorm output.');
        }

        $data = json_decode($matches[0], true);

        if (!is_array($data) || !isset($data['input_i'], $data['input_tp'])) {
            throw new \RuntimeException('FFmpeg loudnorm output does not contain input_i or input_tp.');
        }

        return [
            'integrated_lufs' => round((float) $data['input_i'], 2),
            'true_peak_db' => round((float) $data['input_tp'], 2),
        ];
    }

    public function moveFile(string $from, string $to): void
    {
        $disk = Storage::disk('local');

        if (!$disk->exists($from)) {
            throw new \RuntimeException("File not found: {$from}");
        }

        $disk->makeDirectory(dirname($to));

        if (!$disk->move($from, $to)) {
            throw new \RuntimeException("Could not move file from {$from} to {$to}");
        }
    }

    public function deleteFile(string $path): void
    {
        $disk = Storage::disk('local');

        if (!$disk->exists($path)) {
            throw new \RuntimeException("File not found: {$path}");
        }

        $disk->delete($path);
    }
}
