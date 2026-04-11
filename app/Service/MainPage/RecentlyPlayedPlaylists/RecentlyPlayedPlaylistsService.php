<?php

namespace App\Service\MainPage\RecentlyPlayedPlaylists;

class RecentlyPlayedPlaylistsService implements RecentlyPlayedPlaylistsServiceInterface
{


    public function getRecently() {
        return [];
//        $user = $this->accountService->getCurrentAccount();
//        $playlists = $user->recentlyPlayedPlaylists()->get();
//        return RecentlyPlayedPlaylistsResource::collection($playlists);

    }
}
