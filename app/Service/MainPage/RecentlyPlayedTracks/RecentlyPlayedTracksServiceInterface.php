<?php
namespace App\Service\MainPage\RecentlyPlayedTracks;
use App\Models\RecentlyPlayedTrack;

interface RecentlyPlayedTracksServiceInterface {

    public function getRecently(int $userId);
}
