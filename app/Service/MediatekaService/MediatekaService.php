<?php

namespace App\Service\MediatekaService;

use App\Enum\OrderBy;
use App\Models\Artist;
use App\Models\MediatekaItem;
use App\Models\Playlist;

class MediatekaService implements MediatekaServiceInterface
{

    public function getMediateka(int $userId, OrderBy $orderBy = OrderBy::RECENT, string $query = '')
    {
        $mediatekaQuery = MediatekaItem::query()
            ->where('user_id', $userId)
            ->orderBy('pin_position')
            ->with('libraryable');

        switch ($orderBy) {
            case OrderBy::CREATED_AT:
                $mediatekaQuery->orderByDesc('created_at');
                break;
            default:
        }

        if ($query) {
            $mediatekaQuery->whereHasMorph('libraryable', [Playlist::class, Artist::class], function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            });
        }

        return $mediatekaQuery->get();
    }

    public function addPlaylist(int $userId, int $playlistId)
    {
        $playlist = Playlist::query()->findOrFail($playlistId);

        return MediatekaItem::query()->firstOrCreate([
            'user_id' => $userId,
            'libraryable_type' => $playlist->getMorphClass(),
            'libraryable_id' => $playlist->id,
        ]);
    }
}
