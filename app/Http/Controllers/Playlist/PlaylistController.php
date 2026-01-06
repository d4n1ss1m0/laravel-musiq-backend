<?php

namespace App\Http\Controllers\Playlist;


use App\DTO\AddTrack\AddTrackDTO;
use App\DTO\ArtistDTO;
use App\DTO\CreatePlaylistDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddTrack\AddTrackByFileRequest;
use App\Http\Requests\Playlist\CreatePlaylistRequest;
use App\Http\Resources\Playlists\PlaylistResource;
use App\Http\Resources\Tracks\TrackResource;
use App\Service\FileService\FileServiceInterface;
use App\Service\ImageService\ImageServiceInterface;
use App\Service\MainPage\RecentlyAddedTracks\RecentlyAddedTracksServiceInterface;
use App\Service\MainPage\RecentlyPlayedPlaylists\RecentlyPlayedPlaylistsServiceInterface;
use App\Service\MainPage\RecentlyPlayedTracks\RecentlyPlayedTracksServiceInterface;
use App\Service\PlaylistService\PlaylistService;
use App\Service\TrackService\TrackServiceInterface;
use App\Shared\Enums\PlaylistTypes;
use App\Shared\Traits\HttpResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    use HttpResponse;
    public function __construct(private readonly PlaylistService $playlistService, private readonly FileServiceInterface $fileService)
    {
    }

    public function getPlaylist(int $playlistId, Request $request)
    {
        $userId = $request->get('userId');
        try {
            $playlist = $this->playlistService->getPlaylist($playlistId, $userId);
            return new PlaylistResource($playlist);
        } catch (ModelNotFoundException $e) {
            return $this->error($e->getMessage(), 'NotFound', $e->getCode());
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 'error', $e->getCode());
        }

    }

    public function getTracks(int $playlistId, Request $request)
    {
        $userId = $request->get('userId');
        try {
            $tracks = $this->playlistService->getTracks($playlistId, $userId);
            return TrackResource::collection($tracks);
        } catch (ModelNotFoundException $e) {
            return $this->error($e->getMessage(), 'NotFound', $e->getCode());
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 'error', $e->getCode());
        }
    }

    public function create(CreatePlaylistRequest $request)
    {
        $userId = $request->get('userId');
        try {
            if ($request->file) {
                $cover = $this->fileService->addFile($request->file, 'image/playlist', 'webp');
            }

            $dto = new CreatePlaylistDTO($cover ?? null,
                $request->name,
                PlaylistTypes::tryFrom($request->type),
                $request->tracks);

            $playlist = $this->playlistService->createPlaylist($dto, $userId);

            return $this->success(new PlaylistResource($playlist));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 'error', $e->getCode());
        }
    }
}
