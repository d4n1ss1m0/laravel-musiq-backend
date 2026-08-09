<?php

namespace App\Service\PlaybackService;

use App\DTO\AddTrack\AddTrackDTO;
use App\DTO\AddTrack\AddTrackLinkDTO;
use App\DTO\Playback\SnapshotDTO;
use App\Enum\MusicService;
use App\Enum\PlaybackSource;
use App\Models\Track;

interface PlaybackServiceInterface
{
    public function snapshot(SnapshotDTO $dto, int $userId);
}
