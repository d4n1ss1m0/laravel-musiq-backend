<?php

namespace App\Console\Commands;

use App\Models\Track;
use App\Service\FileService\FileServiceInterface;
use Illuminate\Console\Command;

class ReindexTrackLoudness extends Command
{
    protected $signature = 'tracks:reindex-loudness
        {--uuid= : Reindex a single track by UUID}
        {--only-missing : Reindex only tracks without loudness values}
        {--chunk=100 : Number of tracks to process per chunk}
        {--timeout=600 : FFmpeg timeout per track in seconds}';

    protected $description = 'Reindex tracks loudness metadata: integrated_lufs and true_peak_db';

    public function handle(): int
    {
        $chunkSize = max(1, (int) $this->option('chunk'));
        $timeout = max(1, (int) $this->option('timeout'));
        $fileService = app()->make(FileServiceInterface::class);
        $processed = 0;
        $failed = 0;

        $query = Track::query();

        if ($uuid = $this->option('uuid')) {
            $query->where('uuid', $uuid);
        }

        if ($this->option('only-missing')) {
            $query->where(function ($query) {
                $query
                    ->whereNull('integrated_lufs')
                    ->orWhereNull('true_peak_db');
            });
        }

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->info('No tracks found.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunkById($chunkSize, function ($tracks) use (&$processed, &$failed, $fileService, $timeout, $bar) {
            foreach ($tracks as $track) {
                try {
                    $metadata = $fileService->analyzeMusicFile(
                        storage_path('app/audio/' . $track->song),
                        $timeout
                    );

                    $track->integrated_lufs = $metadata['integrated_lufs'];
                    $track->true_peak_db = $metadata['true_peak_db'];
                    $track->save();

                    $processed++;
                } catch (\Throwable $exception) {
                    $failed++;
                    $this->newLine();
                    $this->error("Track {$track->uuid} failed: {$exception->getMessage()}");
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("Processed: {$processed}");

        if ($failed > 0) {
            $this->warn("Failed: {$failed}");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
