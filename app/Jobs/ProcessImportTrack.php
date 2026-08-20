<?php

namespace App\Jobs;

use App\Models\TrackImport;
use App\Models\TrackImportArtist;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Service\FileService\FileServiceInterface;
use getID3;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ProcessImportTrack implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $id)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $repository = app()->make(TrackRepositoryInterface::class);
        $fileService = app()->make(FileServiceInterface::class);
        $getID3 = new getID3();
        $importTrack = TrackImport::query()
            ->where('id', $this->id)
            ->firstOrFail();

        $file = $importTrack->song;
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'ogg') {
            $file = $fileService->convertMusicFile(storage_path('app/audio/'.$file));
            $importTrack->song = $file;
            $importTrack->save();
        }


        $filePath = storage_path('app/audio/'.$file);
        $loudnessMetadata = $fileService->analyzeMusicFile($filePath);
        $fileInfo = $getID3->analyze($filePath);
        $time = (int)round($fileInfo['playtime_seconds']);

        DB::beginTransaction();
        try {
            $finalTrack = $repository->create(
                $importTrack->name,
                $time,
                $file,
                $importTrack->image,
                $importTrack->text,
                $importTrack->is_private,
                $importTrack->user_id
            );
            $finalTrack->integrated_lufs = $loudnessMetadata['integrated_lufs'];
            $finalTrack->true_peak_db = $loudnessMetadata['true_peak_db'];
            $finalTrack->save();

            $existsArtistsArray = TrackImportArtist::query()
                ->where('track_id', $this->id)
                ->pluck('artist_id')
                ->toArray();

            $finalTrack->artists()->syncWithoutDetaching($existsArtistsArray);

            $importTrack->status = "success";
            $importTrack->save();
            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }


    }

    public function failed(\Throwable $exception): void
    {
        TrackImport::query()
            ->where('id', $this->id)
            ->update([
                'status' => 'failed',
            ]);
    }
}
