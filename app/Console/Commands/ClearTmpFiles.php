<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;

class ClearTmpFiles extends Command
{
    protected $signature = 'tmp:clear {--minutes=30 : Delete files older than this many minutes} {--dry-run : Show files without deleting them}';

    protected $description = 'Clear temporary audio and image files';

    public function handle(): int
    {
        $minutes = max(0, (int) $this->option('minutes'));
        $dryRun = (bool) $this->option('dry-run');
        $threshold = now()->subMinutes($minutes)->getTimestamp();

        $directories = [
            'audio/tmp',
            'image/tmp',
        ];

        $deletedFiles = 0;
        $deletedBytes = 0;
        $errors = 0;

        foreach ($directories as $directory) {
            $path = storage_path("app/{$directory}");

            if (! File::isDirectory($path)) {
                $this->line("Skipped missing directory: {$directory}");
                continue;
            }

            foreach (File::allFiles($path, true) as $file) {
                if ($file->getMTime() > $threshold) {
                    continue;
                }

                $filePath = $file->getPathname();
                $fileSize = $file->getSize();

                if ($dryRun) {
                    $this->line("Would delete: {$filePath}");
                    $deletedFiles++;
                    $deletedBytes += $fileSize;
                    continue;
                }

                try {
                    File::delete($filePath);
                    $deletedFiles++;
                    $deletedBytes += $fileSize;
                } catch (Throwable $exception) {
                    $errors++;
                    $this->error("Failed to delete {$filePath}: {$exception->getMessage()}");
                }
            }

            if (! $dryRun) {
                $this->deleteEmptyDirectories($path);
            }
        }

        $mode = $dryRun ? 'Matched' : 'Deleted';
        $this->info("{$mode} {$deletedFiles} file(s), {$deletedBytes} byte(s).");

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function deleteEmptyDirectories(string $rootPath): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if (! $item->isDir()) {
                continue;
            }

            $directory = $item->getPathname();

            if (count(scandir($directory)) === 2) {
                rmdir($directory);
            }
        }
    }
}
