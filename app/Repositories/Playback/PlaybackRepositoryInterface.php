<?php

namespace App\Repositories\Playback;



use App\Contracts\MediatekaLibraryable;
use App\Enum\MediatekaItemType;
use App\Enum\PlaybackSource;
use App\Enum\PlaybackState;
use App\Enum\RepeatType;
use App\Models\Mediateka\MediatekaItem;
use App\Models\PlaybackSession\PlaybackSession;
use Illuminate\Support\Collection;

interface PlaybackRepositoryInterface
{
    public function createSession(
        PlaybackSource $playbackSource,
        int $sourceId,
        int $currentTrackId,
        bool $shuffle,
        RepeatType $repeatType,
        int $userId
    ): PlaybackSession;

    public function addSourceTracks(int $playbackSessionId, Collection $tracks): void;

    public function getByUserId(int $userId): PlaybackSession|null;
}
