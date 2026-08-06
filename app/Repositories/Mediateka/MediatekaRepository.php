<?php

namespace App\Repositories\Mediateka;

use App\Contracts\MediatekaLibraryable;
use App\Enum\MediatekaItemType;
use App\Models\Mediateka\MediatekaItem;
use Carbon\Carbon;

class MediatekaRepository implements MediatekaRepositoryInterface
{

    public function addMedia(MediatekaLibraryable $media, int $userId): void
    {
        MediatekaItem::create([
            'user_id' => $userId,
            'libraryable_type' => get_class($media),
            'libraryable_id' => $media->getKey()
        ]);
    }

    public function removeMedia(MediatekaLibraryable $media, int $userId): void
    {
        MediatekaItem::query()
            ->where('libraryable_type', get_class($media))
            ->where('libraryable_id', $media->getKey())
            ->where('user_id', $userId)
            ->delete();
    }

    public function getMediatekaItem(MediatekaItemType $mediatekaItems, string $mediatekaItemId, $userId): ?MediatekaItem
    {
        $itemId = ($mediatekaItems->getModel())::query()
            ->where('uuid', $mediatekaItemId)
            ->value('id');

        return MediatekaItem::query()
            ->where('libraryable_type', $mediatekaItems->getModel())
            ->where('libraryable_id', $itemId)
            ->where('user_id', $userId)
            ->first();
    }

    public function pinItem(MediatekaItem $item, int $pinPosition): void
    {
        $item->update([
            'pinned_at' => Carbon::now(),
            'pin_position' => $pinPosition
        ]);
    }

    public function unpinItem(MediatekaItem $item): void
    {
        $item->update([
            'pinned_at' => null,
            'pin_position' => null
        ]);
    }

    public function getMaxPinValue(int $userId): int
    {
        return MediatekaItem::query()
            ->where('user_id', $userId)
            ->max('pin_position') ?? 0;
    }


}
