<?php
namespace App\Service\MainPage\RecentlyPlayedPlaylists;
interface RecentlyPlayedPlaylistsServiceInterface {
    public function getMostPlayablePlaylists(int $userId, int $limit = 4);
}
