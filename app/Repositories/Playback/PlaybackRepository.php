<?php

namespace App\Repositories\Playback;

use App\Contracts\MediatekaLibraryable;
use App\Enum\MediatekaItemType;
use App\Enum\PlaybackSource;
use App\Enum\PlaybackState;
use App\Enum\RepeatType;
use App\Models\Mediateka\MediatekaItem;
use App\Models\PlaybackSession\PlaybackSession;
use App\Models\PlaybackSession\PlaybackSessionTrack;
use App\Repositories\Mediateka\MediatekaRepositoryInterface;
use App\Service\PlaybackService\PlaybackServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PlaybackRepository implements PlaybackRepositoryInterface
{

    public function createSession(
        PlaybackSource $playbackSource,
        int $sourceId,
        int $currentTrackId,
        bool $shuffle,
        RepeatType $repeatType,
        int $userId
    ) : PlaybackSession
    {
        return PlaybackSession::query()
            ->create([
                'user_id' => $userId,
                'source_type' => $playbackSource->getModel(),
                'source_id' => $sourceId,
                'current_track_id' => $currentTrackId,
                'shuffle' => $shuffle,
                'repeat_mode' => $repeatType->value,
                'state' => PlaybackState::PLAYING->value,
                'current_position' => 1,
            ]);
    }

    public function addSourceTracks(int $playbackSessionId, Collection $tracks): void
    {
        $insertData = [];
        $playbackPosition = 1;

        foreach ($tracks as $track) {
            $insertData[] = [
                'session_id' => $playbackSessionId,
                'track_id' => $track['id'],
                'source_position' => $track['position'],
                'playback_position' => $playbackPosition,
            ];

            $playbackPosition++;
        }

        PlaybackSessionTrack::query()->insert($insertData);
    }

    public function getByUserId(int $userId): PlaybackSession|null
    {
        return PlaybackSession::query()
            ->where('user_id', $userId)
            ->first();
    }
}
