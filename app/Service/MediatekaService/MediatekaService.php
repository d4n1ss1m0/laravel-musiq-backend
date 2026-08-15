<?php

namespace App\Service\MediatekaService;

use App\Contracts\MediatekaLibraryable;
use App\Enum\MediatekaItemType;
use App\Enum\OrderBy;
use App\Models\Artist;
use App\Models\ListeningHistoryItem;
use App\Models\Mediateka\MediatekaItem;
use App\Models\Playlist;
use App\Repositories\Artist\ArtistRepositoryInterface;
use App\Repositories\Mediateka\MediatekaRepositoryInterface;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use Illuminate\Support\Facades\DB;

class MediatekaService implements MediatekaServiceInterface
{

    const MAX_PIN = 5;

    public function __construct(
        private readonly MediatekaRepositoryInterface $repository,
        private readonly ArtistRepositoryInterface $artistRepository,
        private readonly PlaylistRepositoryInterface $playlistRepository,
    )
    {
    }

    public function getMediateka(int $userId, OrderBy $orderBy = OrderBy::RECENT, string $query = '')
    {
        $mediatekaQuery = MediatekaItem::query()
            ->where('user_library_items.user_id', $userId)
            ->orderByRaw('pin_position IS NULL')
            ->orderBy('pin_position')
            ->with('libraryable');

        switch ($orderBy) {
            case OrderBy::CREATED_AT:
                $mediatekaQuery->orderByDesc('created_at');
                break;
            case OrderBy::RECENT:
                $recentHistory = ListeningHistoryItem::query()
                    ->selectRaw('source_type, source_id, MAX(last_played_at) as recent_played_at')
                    ->where('user_id', $userId)
                    ->whereNotNull('source_type')
                    ->whereNotNull('source_id')
                    ->groupBy('source_type', 'source_id');

                $mediatekaQuery
                    ->leftJoinSub($recentHistory, 'recent_history', function ($join) {
                        $join->on('recent_history.source_type', '=', 'user_library_items.libraryable_type')
                            ->on('recent_history.source_id', '=', 'user_library_items.libraryable_id');
                    })
                    ->select('user_library_items.*')
                    ->orderByRaw('recent_history.recent_played_at IS NULL')
                    ->orderByDesc('recent_history.recent_played_at')
                    ->orderByDesc('user_library_items.created_at');

                break;
            case OrderBy::ALPAHABET:
                $mediatekaQuery
                    ->leftJoin('playlists', function ($join) {
                        $join->on('playlists.id', '=', 'user_library_items.libraryable_id')
                            ->where('user_library_items.libraryable_type', Playlist::class);
                    })
                    ->leftJoin('artists', function ($join) {
                        $join->on('artists.id', '=', 'user_library_items.libraryable_id')
                            ->where('user_library_items.libraryable_type', Artist::class);
                    })
                    ->select('user_library_items.*')
                    ->orderByRaw('LOWER(COALESCE(playlists.name, artists.name)) ASC');

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

    public function addMedia(MediatekaItemType $mediatekaType, string $mediaId, int $userId)
    {
        DB::transaction(function () use ($userId, $mediaId, $mediatekaType) {
            $mediatekaItem = $this->repository->getMediatekaItem(
                $mediatekaType,
                $mediaId,
                $userId
            );

            if ($mediatekaItem) {
                throw new \Exception('This media already in mediateka');
            }

            $media = $this->getLibraryableByUUID($mediatekaType, $mediaId);
            if (!$media) {
                throw new \Exception('Media not found');
            }

            $this->repository->addMedia($media, $userId);
        });
    }

    public function removeMedia(MediatekaItemType $mediatekaType, string $mediaId, int $userId)
    {
        DB::transaction(function () use ($userId, $mediaId, $mediatekaType) {
            $mediatekaItem = $this->repository->getMediatekaItem(
                $mediatekaType,
                $mediaId,
                $userId
            );

            if (!$mediatekaItem) {
                throw new \Exception('This media not in mediateka');
            }

            $this->repository->removeMedia($mediatekaItem->libraryable, $userId);
        });
    }

    private function getLibraryableByUUID(MediatekaItemType $mediatekaType, string $uuid): ?MediatekaLibraryable
    {
        return match ($mediatekaType) {
            MediatekaItemType::ARTIST => $this->artistRepository->getByUUID($uuid),
            MediatekaItemType::PLAYLIST => $this->playlistRepository->getByUUID($uuid),
        };
    }

    public function pinMedia(MediatekaItemType $mediatekaType, string $mediaId, int $userId)
    {
        DB::transaction(function () use ($userId, $mediatekaType, $mediaId) {
            $maxPin = $this->repository->getMaxPinValue($userId);

            if ($maxPin == self::MAX_PIN) {
                throw new \Exception('Maximum number of pins reached');
            }

            $mediatekaItem = $this->repository->getMediatekaItem($mediatekaType, $mediaId, $userId);

            if (!$mediatekaItem) {
                throw new \Exception('Media not found in mediateka');
            }

            if ($mediatekaItem->isPinned()) {
                throw new \Exception('This media is already pinned');
            }

            $this->repository->pinItem($mediatekaItem, ++$maxPin);
        });
    }

    public function unpinMedia(MediatekaItemType $mediatekaType, string $mediaId, int $userId)
    {
        DB::transaction(function () use ($userId, $mediatekaType, $mediaId) {
            $mediatekaItem = $this->repository->getMediatekaItem($mediatekaType, $mediaId, $userId);

            if (!$mediatekaItem) {
                throw new \Exception('Media not found in mediateka');
            }

            if (!$mediatekaItem->isPinned()) {
                throw new \Exception('This media is not pinned');
            }

            $this->repository->unpinItem($mediatekaItem);
        });
    }
}
