<?php

namespace App\Repositories\Mediateka;



use App\Contracts\MediatekaLibraryable;
use App\Enum\MediatekaItemType;
use App\Models\Mediateka\MediatekaItem;

interface MediatekaRepositoryInterface
{
    public function addMedia(MediatekaLibraryable $media, int $userId): void;
    public function removeMedia(MediatekaLibraryable $media, int $userId): void;
    public function getMediatekaItem(MediatekaItemType $mediatekaItems, string $mediatekaItemId, int $userId): ?MediatekaItem;
    public function getMaxPinValue(int $userId): int;
    public function pinItem(MediatekaItem $item, int $pinPosition): void;
    public function unpinItem(MediatekaItem $item): void;
}
