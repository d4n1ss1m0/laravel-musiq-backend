<?php

namespace App\Service\HistoryService;

use App\DTO\History\HistoryItemDTO;
use App\Enum\HistorySource;
use App\Repositories\Artist\ArtistRepositoryInterface;
use App\Repositories\History\HistoryRepositoryInterface;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Repositories\Track\TrackRepositoryInterface;
use Carbon\Carbon;

class HistoryService implements HistoryServiceInterface
{
    private int $playCooldown;
    public function __construct(
        private readonly HistoryRepositoryInterface $repository,
        private readonly PlaylistRepositoryInterface $playlistRepository,
        private readonly ArtistRepositoryInterface $artistRepository,
        private readonly TrackRepositoryInterface $trackService,
    )
    {
        $this->playCooldown = (int) config('services.history_service.play_cooldown', 1);
        //        $this->playCooldown = 1;
    }

    public function store(int $userId, string $trackId, $sourceId, HistorySource|null $sourceType): void
    {
        $date = Carbon::now();

        if (!is_null($sourceType)) {
            $sourceId = match ($sourceType) {
                HistorySource::PLAYLIST => $this->playlistRepository->getByUUID($sourceId)->id,
                HistorySource::ARTIST => $this->artistRepository->getByUUID($sourceId)->id,
            };
            $sourceType = $sourceType->getModel();
        }

        $trackId = $this->trackService->getTrackByUuids([$trackId])[0]->id;

        $dto = new HistoryItemDTO(
            $userId,
            $trackId,
            $sourceType,
            $sourceId
        );

        $item = $this->repository->get($dto, $date);

        if (!$item) {
            $this->repository->store($dto);
            return;
        }

        //Проверяем что воспроизведение было позже чем 1 минута, чтобы избежать накрутки наглой
        $lastPlayTime = $item->last_played_at;

        if (
            $lastPlayTime
            && $lastPlayTime <= Carbon::now()->subMinutes($this->playCooldown)
        ) {
            $this->repository->incrementListen($item);
        }
    }
}
