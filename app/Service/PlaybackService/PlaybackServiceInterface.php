<?php

namespace App\Service\PlaybackService;

use App\DTO\AddTrack\AddTrackDTO;
use App\DTO\AddTrack\AddTrackLinkDTO;
use App\DTO\Playback\SnapshotDTO;
use App\Enum\MusicService;
use App\Enum\PlaybackSource;
use App\Enum\PlaybackState;
use App\Enum\RepeatType;
use App\Models\PlaybackSession\PlaybackSession;
use App\Models\Track;

interface PlaybackServiceInterface
{
    public function snapshot(SnapshotDTO $dto, int $userId) : PlaybackSession;
    public function shuffle(bool $shuffle, int $userId) : PlaybackSession;
    public function next(int $userId, bool $requeue) : PlaybackSession;
    public function previous(int $userId) : PlaybackSession;
    public function repeat(RepeatType $repeatType, int $userId) : PlaybackSession;
    public function changeState(PlaybackState $state, int $userId) : PlaybackSession;
}
