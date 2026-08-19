<?php

namespace App\Console\Commands;

use App\Models\RecentlyPlayedTrack;
use App\Models\Track;
use App\Models\TrackArtists;
use App\Models\TrackPlaylist;
use App\Service\FileService\FileServiceInterface;
use Illuminate\Console\Command;

class DeleteTrack extends Command
{
    protected $signature = 'track:delete {uuid}';

    protected $description = 'Clear temporary audio and image files';

    public function handle(): int
    {
        $fileService = app()->make(FileServiceInterface::class);
        $uuid = $this->argument('uuid');
        $track = Track::where('uuid', $uuid)->first();

        if (!$track) {
            $this->error("Track not found: {$uuid}");

            return self::FAILURE;
        }

        if ($track->song) {
            $audio = 'audio/' . $track->song;
            $fileService->deleteFile($audio);
        }
        if ($track->image) {
            $image = 'image/track/' . $track->image;
            $fileService->deleteFile($image);
        }

        $track->delete();
        TrackPlaylist::query()
            ->where('track_id', $track->id)
            ->delete();

        RecentlyPlayedTrack::query()
            ->where('track_id', $track->id)
            ->delete();

        TrackArtists::query()
            ->where('track_id', $track->id)
            ->delete();

        $this->info("Track deleted: {$uuid}");

        return self::SUCCESS;
    }
}
