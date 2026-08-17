<?php

namespace App\Http\Controllers\MainPage;

use App\Http\Resources\Playlists\PlaylistResource;
use App\Models\Playlist;
use App\Repositories\Playlist\PlaylistRepositoryInterface;
use App\Service\PlaylistService\PlaylistServiceInterface;
use App\Shared\Fields\Fields;
use App\Http\Controllers\Controller;
use App\Http\Requests\Utility\PaginateRequest;
use App\Http\Resources\Tracks\TrackResource;
use App\Service\MainPage\RecentlyAddedTracks\RecentlyAddedTracksServiceInterface;
use App\Service\MainPage\RecentlyPlayedPlaylists\RecentlyPlayedPlaylistsServiceInterface;
use App\Service\MainPage\RecentlyPlayedTracks\RecentlyPlayedTracksServiceInterface;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class MainPageController extends Controller
{
    use HttpResponse;

    public function __construct(
        private readonly RecentlyPlayedTracksServiceInterface $recentlyPlayedTracksService,
        private readonly RecentlyPlayedPlaylistsServiceInterface $recentlyPlayedPlaylistsService,
        private readonly RecentlyAddedTracksServiceInterface $recentlyAddedTracksService,
        private readonly PlaylistServiceInterface $playlistServiceInterface
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

    public function getRecentlyAddedTracks(PaginateRequest $request) {
        try {
            $perPage = $request->query(Fields::PER_PAGE, config('app.per_page_default'));
            $tracks = $this->recentlyAddedTracksService->getRecently($perPage);
            $keyValueArray = [
                Fields::ITEMS_IDS => array_column($tracks->items(), 'uuid'),
                Fields::ITEMS  => TrackResource::collection($tracks),
            ];

            return $this->success($this->paginator($keyValueArray, $tracks->total(), $tracks->perPage(), $tracks->currentPage()));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getMostPlayablePlaylists(Request $request)
    {
        try {
            $userId = $request->attributes->get('userId');
            $playlists = $this->recentlyPlayedPlaylistsService->getMostPlayablePlaylists($userId, 4);
            $playlists = PlaylistResource::collection($playlists)->resolve();
            $favourite = (new PlaylistResource($this->playlistServiceInterface->getFavouritePlaylist($userId)))->resolve();
            $history = [
                'id' => 'history',
                'name' => 'history',
                'image' => [],
                'type' => [
                    'id' => 'history',
                    'name' => 'history',
                ]
            ];
            $array = [];
            array_push($array, $favourite);
            array_push($array, $history);
            $array = array_merge($array, $playlists);
            return $this->success($array);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
