<?php

namespace App\Service\MainPage\RecentlyPlayedPlaylists;

use App\Models\ListeningHistoryItem;
use App\Models\Playlist;
use Illuminate\Support\Collection;

class RecentlyPlayedPlaylistsService implements RecentlyPlayedPlaylistsServiceInterface
{


    public function getRecently() {
        return [];
//        $user = $this->accountService->getCurrentAccount();
//        $playlists = $user->recentlyPlayedPlaylists()->get();
//        return RecentlyPlayedPlaylistsResource::collection($playlists);

    }

    public function getMostPlayablePlaylists(int $userId, int $limit = 4): Collection
    {
        $mostPlayable = ListeningHistoryItem::query()
            ->where('source_type', Playlist::class)
            ->where('user_id', $userId)
            ->groupBy(['source_type','source_id'])
            ->selectRaw('sum(play_count) as sum, source_type, source_id')
            ->orderBy('sum', 'desc');

        $playlists = Playlist::query()
            ->joinSub($mostPlayable, 'mostPlayable', function ($join) {
                $join->on('mostPlayable.source_id', '=', 'playlists.id');
            })
            ->get();

        return $playlists;
    }
}
