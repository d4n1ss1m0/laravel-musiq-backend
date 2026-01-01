<?php

namespace App\Http\Controllers\MainPage;


use App\DTO\AddTrack\AddTrackDTO;
use App\DTO\ArtistDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddTrack\AddTrackByFileRequest;
use App\Service\ImageService\ImageServiceInterface;
use App\Service\MainPage\RecentlyAddedTracks\RecentlyAddedTracksServiceInterface;
use App\Service\MainPage\RecentlyPlayedPlaylists\RecentlyPlayedPlaylistsServiceInterface;
use App\Service\MainPage\RecentlyPlayedTracks\RecentlyPlayedTracksServiceInterface;
use App\Service\TrackService\TrackServiceInterface;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class MainPageController extends Controller
{
    use HttpResponse;

    public function __construct(
        private readonly RecentlyPlayedTracksServiceInterface $recentlyPlayedTracksService,
        private readonly RecentlyPlayedPlaylistsServiceInterface $recentlyPlayedPlaylistsService,
        private readonly RecentlyAddedTracksServiceInterface $recentlyAddedTracksService
    )
    {
    }

    public function getRecentlyPlayedTracks(Request $request) {
        try {
            return $this->success($this->recentlyPlayedTracksService->getRecently($request->get('userId')));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getRecentlyPlayedPlaylists() {
        try {
            return $this->success($this->recentlyPlayedPlaylistsService->getRecently());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getRecentlyAddedTracks() {
        try {
            return $this->success($this->recentlyAddedTracksService->getRecently());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
