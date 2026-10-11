<?php

namespace App\Service\PlaybackService;

use App\DTO\Playback\SnapshotDTO;
use App\Enum\PlaybackManualType;
use App\Enum\PlaybackSource;
use App\Enum\PlaybackState;
use App\Enum\RepeatType;
use App\Enum\SwitchTrackType;
use App\Exceptions\NotFoundException;
use App\Models\Auth\User;
use App\Models\PlaybackSession\PlaybackSession;
use App\Models\PlaybackSession\PlaybackSessionTrack;
use App\Models\TrackPlaylist;
use App\Repositories\Artist\ArtistRepositoryInterface;
use App\Repositories\Playback\PlaybackRepositoryInterface;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Repositories\Track\TrackRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlaybackService implements PlaybackServiceInterface
{

    public function __construct(
        private readonly PlaylistRepositoryInterface $playlistRepository,
        private readonly TrackRepositoryInterface $trackRepository,
        private readonly ArtistRepositoryInterface $artistRepository,
        private readonly PlaybackRepositoryInterface $repository
    )
    {
    }

    private function lockPlaybackUser(int $userId): void
    {
        User::query()->whereKey($userId)->lockForUpdate()->firstOrFail();
    }

    private function withLockedSession(int $userId, callable $callback): mixed
    {
        return DB::transaction(function () use ($userId, $callback) {
            $this->lockPlaybackUser($userId);

            $session = $this->repository->getByUserId($userId);
            if (!$session) {
                throw new NotFoundException('Session not found');
            }

            return $callback($session);
        });
    }

    public function snapshot(SnapshotDTO $dto, int $userId) : PlaybackSession
    {
        $tracks = $this->getTracks($dto->source, $dto->sourceId, $dto->trackId, $dto->shuffle);
        $currentTrack = $tracks->firstWhere('uuid', $dto->trackId);
        $currentTrackId = $currentTrack['id'];
        $currentPosition = $currentTrack['position'];
        if ($dto->shuffle) {
            $currentPosition = 1;
        }

        $sourceDbId = $this->getSourceDbId($dto->source, $dto->sourceId);

        return DB::transaction(function () use ($dto, $userId, $tracks, $sourceDbId, $currentTrackId, $currentPosition) {
            $this->lockPlaybackUser($userId);

            $session = $this->repository->getByUserId($userId);
            if ($session) {
                $session->delete();
            }

            $session = $this->repository->createSession(
                $dto->source,
                $sourceDbId,
                $currentTrackId,
                $currentPosition,
                $dto->shuffle,
                $dto->repeatType,
                $userId
            );

            $this->repository->addSourceTracks($session->id, $tracks);

            return $session;
        });
    }

    private function getTracks(PlaybackSource $type, string $sourceId, string $currentTrackId, bool $shuffle = false)
    {
        $tracks = match ($type) {
            PlaybackSource::PLAYLIST => $this->playlistRepository->getPlaylistTracks($sourceId),
            PlaybackSource::ARTIST => $this->artistRepository->getTracks($sourceId),
            PlaybackSource::TRACK => $this->trackRepository->getTrackByUuids([$sourceId])
        };

        $tracksArray = [];
        $position = 1;

        foreach ($tracks as $track) {
            $tracksArray[] = [
                'id' => $track->id,
                'uuid' => $track->uuid,
                'position' => $position,
            ];

            $position++;
        }

        $tracks = collect($tracksArray);

        if ($shuffle) {
            $currentTrack = $tracks->firstWhere('uuid', $currentTrackId);
            $tracks = $tracks->reject(fn ($track) => $track['uuid'] === $currentTrackId)
                ->values();

            $tracks = $tracks->shuffle();
            $tracks = $tracks->prepend([
                    'id' => $currentTrack['id'],
                    'uuid' => $currentTrack['uuid'],
                    'position' => $currentTrack['position']
                ]);
        }

        return $tracks;
    }
    private function getSourceDbId(PlaybackSource $type, string $sourceId)
    {
        return match ($type) {
            PlaybackSource::PLAYLIST => $this->playlistRepository->getByUUID($sourceId)->id,
            PlaybackSource::ARTIST => $this->artistRepository->getByUUID($sourceId)->id,
            PlaybackSource::TRACK => $this->trackRepository->getTrackByUuids([$sourceId])[0]->id
        };
    }

    public function shuffle(bool $shuffle, int $userId) : PlaybackSession
    {
        return $this->withLockedSession($userId, function (PlaybackSession $session) use ($shuffle) {
            if ($session->shuffle == $shuffle) {
                return $session;
            }

            $session->shuffle = $shuffle;

            $currentTrackPosition = $session->current_position;

            if ($shuffle) {
                $this->shuffleTracks($session, $currentTrackPosition);
            } else {
                $this->unshuffleTracks($session, $currentTrackPosition);
            }

            $session->save();
            $session->load(['source', 'currentTrack', 'sessionTracks.track']);

            return $session;
        });
    }

    private function shuffleTracks(PlaybackSession &$session, int $currentTrackPosition)
    {
        $tracks = $session->sessionTracks;
        $currentTrack = $tracks->firstWhere('playback_position', $currentTrackPosition);
        $tracks = $tracks->reject(fn ($track) => $track->playback_position === $currentTrackPosition)
            ->values();

        $tracks = $tracks->shuffle();

        $tracks->prepend($currentTrack);

        $position = 1;
        foreach ($tracks as $track) {
            $track->playback_position = $position;
            $position++;
        }

        $this->updatePlaybackPositions($session, $tracks);
        $session->current_position = 1;
    }

    private function unshuffleTracks(PlaybackSession &$session, int $currentTrackPosition): void
    {
        $tracks = $session->sessionTracks;
        $currentSourcePosition = $tracks->firstWhere('playback_position', $currentTrackPosition)->source_position;

        $tracks = $tracks->sortBy('source_position')->values();

        $this->updatePlaybackPositions($session, $tracks);
        $session->current_position = $currentSourcePosition;
    }

    private function updatePlaybackPositions(PlaybackSession $session, Collection $tracks): void
    {
        $tracks = $tracks->values();

        DB::transaction(function () use ($session, $tracks) {
            $query = DB::table('playback_session_tracks')->where('session_id', $session->id);
            $offset = (int) (clone $query)->max('playback_position') + 1;
            (clone $query)->increment('playback_position', $offset);

            $case = 'CASE source_position ';
            $bindings = [];

            foreach ($tracks as $index => $track) {
                $case .= 'WHEN ? THEN ? ';
                $bindings[] = (int) $track->source_position;
                $bindings[] = $index + 1;
            }

            $case .= 'ELSE playback_position END';

            $bindings[] = $session->id;

            DB::update(
                "
              UPDATE playback_session_tracks
              SET playback_position = ({$case})::bigint
              WHERE session_id = ?
              ",
                $bindings
            );
        });
    }


    public function next(int $userId, bool $requeue) : PlaybackSession
    {
        return $this->withLockedSession($userId, function (PlaybackSession $session) use ($requeue) {
            $this->switchTrack($session, SwitchTrackType::NEXT, $requeue);
            $session = $this->savePausePosition($session, null);
            $session->save();

            return $session;
        });
    }

    public function previous(int $userId) : PlaybackSession
    {
        return $this->withLockedSession($userId, function (PlaybackSession $session) {
            $this->switchTrack($session, SwitchTrackType::PREVIOUS, true);
            $session = $this->savePausePosition($session, null);
            $session->save();

            return $session;
        });
    }

    private function switchTrack(PlaybackSession $session, SwitchTrackType $switchType, bool $requeue = false) : PlaybackSession
    {
        //Если повтор трек, ничего не двигаем, возращаем текущее
        if ($session->repeat_mode == RepeatType::TRACK) {
            return $session;
        }

        $trackQueue = PlaybackSessionTrack::query()
            ->orderBy('playback_position', $switchType->getOrder())
            ->where('session_id', $session->id);

        $nextTrack = (clone $trackQueue)
            ->where('playback_position', $switchType->getOffsetSign(), $session->current_position)
            ->first();

        //Если текущий трек - конец очереди, то нужно проверить, есть ли повтор
        //Если повтора нет, и нет перезапуска, то завершаем воспроизведение
        if (!$nextTrack && $session->repeat_mode == RepeatType::OFF && !$requeue) {
            $session->state = PlaybackState::FINISHED;
            return $session;
        }

        if ($session->state == PlaybackState::FINISHED) {
            $session->state = PlaybackState::PLAYING;
        }

        //Если текущий трек - конец очереди, и есть перезапуск или повтор очереди
        //Берем первый трек из очереди
        if (!$nextTrack) {
            $nextTrack = (clone $trackQueue)->first();
        }

        $session->current_position = $nextTrack->playback_position;
        $session->current_track_id = $nextTrack->track_id;

        return $session;
    }

    public function repeat(RepeatType $repeatType, int $userId) : PlaybackSession
    {
        return $this->withLockedSession($userId, function (PlaybackSession $session) use ($repeatType) {
            $session->repeat_mode = $repeatType;
            $session->save();

            return $session;
        });
    }

    public function play(int $userId, int|null $trackPlacement): PlaybackSession
    {
        return $this->withLockedSession($userId, function (PlaybackSession $session) use ($trackPlacement) {
            if ($trackPlacement) {
                $session = $this->changeTrack($trackPlacement, $session);
            }

            $session = $this->changeState(PlaybackState::PLAYING, $session);
            $session = $this->savePausePosition($session, null);
            $session->save();

            return $session;
        });
    }

    public function pause(int $userId, int $positionSeconds): PlaybackSession
    {
        return $this->withLockedSession($userId, function (PlaybackSession $session) use ($positionSeconds) {
            $session = $this->savePausePosition($session, $positionSeconds);
            $session = $this->changeState(PlaybackState::PAUSED, $session);
            $session->save();

            return $session;
        });
    }

    private function changeState(PlaybackState $state, PlaybackSession $session) : PlaybackSession
    {
        $session->state = $state;
        return $session;
    }

    private function changeTrack(int $playTrackPlace, PlaybackSession $session) : PlaybackSession
    {
        $sessionTrack = PlaybackSessionTrack::query()
            ->where('session_id', $session->id)
            ->where('playback_position', $playTrackPlace)
            ->first();

        if (!$sessionTrack) {
            throw new NotFoundException('Track in session not found');
        }

        $session->current_position = $sessionTrack->playback_position;
        $session->current_track_id = $sessionTrack->track_id;

        if ($session->source_type == PlaybackSource::TRACK->getModel()) {
            $session->source_id = $sessionTrack->track_id;
        }

        return $session;
    }

    private function savePausePosition(PlaybackSession $session, int|null $positionSeconds) : PlaybackSession
    {
        $session->pause_position_seconds = $positionSeconds;

        return $session;
    }

    public function getQueue(int $userId, int $perPage): array
    {
        $session = $this->repository->getByUserId($userId);

        if (!$session) {
            throw new \Exception("Session not found");
        }

        $tracks = PlaybackSessionTrack::query()
            ->where('session_id', $session->id)
            ->with('track.artists')
            ->orderBy('playback_position')
            ->paginate(perPage: $perPage);

        return [$session, $tracks];
    }

    public function addToQueue(int $userId, PlaybackManualType $type, string $trackId)
    {
        $track = $this->trackRepository->getTrackByUuids([$trackId])[0];

        $this->withLockedSession($userId, function (PlaybackSession $session) use ($track, $type) {
            if ($type == PlaybackManualType::NEXT) {

                $nextTrack = PlaybackSessionTrack::query()
                    ->where('session_id', $session->id)
                    ->where('playback_position', $session->current_position + 1)
                    ->first();

                $sourcePositionQuery = PlaybackSessionTrack::query()
                    ->where('session_id', $session->id)
                    ->where('source_position', '>=', $nextTrack->source_position);
                $sourceOffset = (int) PlaybackSessionTrack::query()
                    ->where('session_id', $session->id)
                    ->max('source_position') + 1;
                (clone $sourcePositionQuery)->increment('source_position', $sourceOffset);

                (clone $sourcePositionQuery)->decrement('source_position', $sourceOffset - 1);


                $playbackPositionQuery = PlaybackSessionTrack::query()
                    ->where('session_id', $session->id)
                    ->where('playback_position', '>=', $nextTrack->playback_position);
                $playbackOffset = (int) PlaybackSessionTrack::query()
                    ->where('session_id', $session->id)
                    ->max('playback_position') + 1;
                (clone $playbackPositionQuery)->increment('playback_position', $playbackOffset);

                (clone $playbackPositionQuery)->decrement('playback_position', $playbackOffset - 1);



                PlaybackSessionTrack::create([
                    'session_id' => $session->id,
                    'source_position' => $nextTrack->source_position,
                    'playback_position' => $nextTrack->playback_position,
                    'track_id' => $track->id,
                    'origin' => 'manual',
                    'placement' => $type->value
                ]);
            } else {
                $maxSourcePosition = PlaybackSessionTrack::query()
                    ->where('session_id', $session->id)
                    ->max('source_position');
                $maxPlaybackPosition = PlaybackSessionTrack::query()
                    ->where('session_id', $session->id)
                    ->max('playback_position');

                PlaybackSessionTrack::create([
                    'session_id' => $session->id,
                    'source_position' => $maxSourcePosition + 1,
                    'playback_position' => $maxPlaybackPosition + 1,
                    'track_id' => $track->id,
                    'origin' => 'manual',
                    'placement' => $type->value
                ]);
            }
        });
    }

    public function moveTrack(int $userId, int $currentPosition, int $position) : PlaybackSession
    {
        return $this->withLockedSession($userId, function (PlaybackSession $session) use ($currentPosition, $position) {
            $currentTrack = PlaybackSessionTrack::query()
                ->where('session_id', $session->id)
                ->where('playback_position', $session->current_position)
                ->first();

            if (!$currentTrack) {
                throw new NotFoundException('Track in session not found');
            }

            $queueTracks = $session->sessionTracks;

            $fromIndex = $queueTracks->search(
                fn ($track) => (int) $track->playback_position === $currentPosition
            );

            if ($fromIndex === false || $position < 1 || $position > $queueTracks->count()) {
                throw new \InvalidArgumentException('Некорректный трек или позиция');
            }

            $track = $queueTracks->splice($fromIndex, 1)->first();
            $queueTracks->splice($position - 1, 0, [$track]);

            $tracks = $queueTracks->values();
            $this->updatePlaybackPositions($session, $tracks);

            $currentTrack = PlaybackSessionTrack::query()
                ->where('id', $currentTrack->id)
                ->first();
            $session->current_position = $currentTrack->playback_position;

            if (!$session->shuffle) {
                $query = DB::table('playback_session_tracks')->where('session_id', $session->id);
                $offset = (int) (clone $query)->max('source_position') + 1;
                (clone $query)->increment('source_position', $offset);

                (clone $query)->update(['source_position' => DB::raw('playback_position')]);
            }

            $session->save();

            return $session;
        });
    }
}
