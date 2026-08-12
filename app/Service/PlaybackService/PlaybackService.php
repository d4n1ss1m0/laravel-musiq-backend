<?php

namespace App\Service\PlaybackService;

use App\DTO\AddTrack\AddTrackLinkDTO;
use App\DTO\Playback\SnapshotDTO;
use App\Enum\MusicService;
use App\Enum\PlaybackSource;
use App\Enum\PlaybackState;
use App\Enum\RepeatType;
use App\Enum\SwitchTrackType;
use App\Models\PlaybackSession\PlaybackSession;
use App\Models\PlaybackSession\PlaybackSessionTrack;
use App\Models\Playlist;
use App\Models\Track;
use App\DTO\AddTrack\AddTrackDTO;
use App\Repositories\Artist\ArtistRepositoryInterface;
use App\Repositories\Playback\PlaybackRepositoryInterface;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Service\FileService\FileServiceInterface;
use App\Service\TrackService\TrackServiceInterface;
use getID3;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

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
        $session = $this->repository->getByUserId($userId);
        if (!$session) {
            throw new \Exception("Session not found");
        }

        if ($session->shuffle == $shuffle) {
            return $session;
        }

        $session->shuffle = $shuffle;

        $currentTrackPosition = $session->current_position;
        $currentTrackId = $session->current_track_id;

        if ($shuffle) {
            $this->shuffleTracks($session, $currentTrackPosition, $currentTrackId);
        } else {
            $this->unshuffleTracks($session, $currentTrackPosition, $currentTrackId);
        }

        $session->save();

        return $session;
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
            DB::table('playback_session_tracks')
                ->where('session_id', $session->id)
                ->update([
                    'playback_position' => DB::raw('playback_position + 1000000'),
                ]);

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
        return $this->switchTrack($userId, SwitchTrackType::NEXT, $requeue);
    }

    public function previous(int $userId) : PlaybackSession
    {
        return $this->switchTrack($userId, SwitchTrackType::PREVIOUS, true);
    }

    private function switchTrack(int $userId, SwitchTrackType $switchType, bool $requeue = false)
    {
        $session = $this->repository->getByUserId($userId);

        if (!$session) {
            throw new \Exception("Session not found");
        }

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
            $session->save();
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
        $session->save();
        return $session;
    }

    public function repeat(RepeatType $repeatType, int $userId) : PlaybackSession
    {
        $session = $this->repository->getByUserId($userId);
        $session->repeat_mode = $repeatType;
        $session->save();
        return $session;
    }

    public function changeState(PlaybackState $state, int $userId) : PlaybackSession
    {
        $session = $this->repository->getByUserId($userId);
        $session->state = $state;
        $session->save();
        return $session;
    }
}
