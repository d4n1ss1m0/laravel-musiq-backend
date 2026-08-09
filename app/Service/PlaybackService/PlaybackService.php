<?php

namespace App\Service\PlaybackService;

use App\DTO\AddTrack\AddTrackLinkDTO;
use App\DTO\Playback\SnapshotDTO;
use App\Enum\MusicService;
use App\Enum\PlaybackSource;
use App\Models\PlaybackSession\PlaybackSession;
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

    public function snapshot(SnapshotDTO $dto, int $userId)
    {
        $tracks = $this->getTracks($dto->source, $dto->sourceId, $dto->shuffle);

        $sourceDbId = $this->getSourceDbId($dto->source, $dto->sourceId);

        $currentTrackId = $tracks[0]['id'];

        $session = PlaybackSession::query()
            ->where('user_id', $userId)
            ->first();

        if ($session) {
            $session->delete();
        }

        $session = $this->repository->createSession(
            $dto->source,
            $sourceDbId,
            $currentTrackId,
            $dto->shuffle,
            $dto->repeatType,
            $userId
        );

        $this->repository->addSourceTracks($session->id, $tracks);
    }

    private function getTracks(PlaybackSource $type, string $sourceId, bool $shuffle = false)
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
                'position' => $position,
            ];

            $position++;
        }

        $tracks = collect($tracksArray);

        if ($shuffle) {
            $tracks = $tracks->shuffle();
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
}
