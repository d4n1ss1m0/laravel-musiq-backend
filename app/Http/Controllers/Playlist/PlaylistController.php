<?php

namespace App\Http\Controllers\Playlist;


use App\DTO\Playlist\CreatePlaylistDTO;
use App\DTO\Playlist\UpdatePlaylistDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Playlist\AddTrackToFavouriteRequest;
use App\Http\Requests\Playlist\AddTrackToPlaylistRequest;
use App\Http\Requests\Playlist\ChangeTrackOrderRequest;
use App\Http\Requests\Playlist\CreatePlaylistRequest;
use App\Http\Requests\Playlist\ImportFromPlaylistRequest;
use App\Http\Requests\Playlist\ManyTracksRequest;
use App\Http\Requests\Playlist\RemoveTrackFromFavouriteRequest;
use App\Http\Requests\Playlist\UpdatePlaylistRequest;
use App\Http\Requests\Utility\SearchPaginateRequest;
use App\Http\Resources\Playlists\PlaylistResource;
use App\Http\Resources\Tracks\TrackResource;
use App\Models\Playlist;
use App\Service\FileService\FileServiceInterface;
use App\Service\PlaylistService\PlaylistServiceInterface;
use App\Shared\Enums\PlaylistTypes;
use App\Shared\Fields\Fields;
use App\Shared\Traits\HttpResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    use HttpResponse;
    public function __construct(private readonly PlaylistServiceInterface $playlistService, private readonly FileServiceInterface $fileService)
    {
    }

    public function getPlaylist(string $playlistId)
    {
        $playlist = $this->playlistService->getPlaylist($playlistId);
        return $this->success(PlaylistResource::make($playlist)->withTracksStats());
    }

    public function getTracks(string $playlistId, SearchPaginateRequest $request)
    {
        $perPage = $request->query(Fields::PER_PAGE, config('app.per_page_default'));
        $query = $request->query(Fields::QUERY);

        $tracks = $this->playlistService->getTracks($playlistId, $perPage, $query);
        $keyValueArray = [
            Fields::ITEMS => TrackResource::collection($tracks),
        ];

        return $this->success(
            $this->paginator(
                $keyValueArray,
                $tracks->total(),
                $tracks->perPage(),
                $tracks->currentPage()
            )
        );
    }

    public function getQueue(string $playlistId, Request $request)
    {
        $tracks = $this->playlistService->getQueue($playlistId);
        return $this->success($tracks);
    }

    public function create(CreatePlaylistRequest $request)
    {
        $userId = $request->get('userId');
        if ($request->hasFile(Fields::FILE)) {
            $cover = $this->fileService->addFile($request->file(Fields::FILE), 'image/playlist', 'webp');
        }

        $dto = new CreatePlaylistDTO($cover ?? null,
            $request->input(Fields::NAME),
            PlaylistTypes::tryFrom($request->input(Fields::TYPE))
        );

        $playlist = $this->playlistService->createPlaylist($dto, $userId);

        return $this->success(PlaylistResource::make($playlist)->withTracksStats());
    }

    public function addTrack(string $playlistId, AddTrackToPlaylistRequest $request)
    {
        $this->playlistService->addTrackToPlaylist($playlistId, $request->input(Fields::IDS));

        return $this->success(['message' => 'Track added', 'playlistId' => $playlistId, 'tracksIds' => $request->input(Fields::IDS)]);
    }

    public function removeTrack(string $playlistId, ManyTracksRequest $request)
    {
        $this->playlistService->removeTrackFromPlaylist($playlistId, $request->input(Fields::IDS));

        return $this->success(['message' => 'Track removed']);
    }

    public function order(string $playlistId, ChangeTrackOrderRequest $request)
    {
        $this->playlistService->changeOrder($playlistId, $request->input(Fields::ID), $request->input(Fields::ORDER));

        return $this->success(['message' => 'Order changed']);
    }

    public function update(string $playlistId, UpdatePlaylistRequest $request)
    {
        if ($request->hasFile(Fields::FILE)) {
            $path = $this->fileService->addFile($request->file(Fields::FILE), 'image/playlist', 'webp');
        }

        $playlistDTO = new UpdatePlaylistDTO(
            $path ?? null,
            $request->input(Fields::NAME),
            $request->input(Fields::TYPE)? PlaylistTypes::tryFrom($request->input(Fields::TYPE)) : null
        );

        $this->playlistService->updatePlaylist($playlistId, $playlistDTO);

        return $this->success(['message' => 'Playlist updated']);
    }

    public function delete(string $playlistId)
    {
        $this->playlistService->deletePlaylist($playlistId);

        return $this->success(['message' => 'Playlist deleted']);
    }

    public function importFromPlaylist(string $playlistId, ImportFromPlaylistRequest $request)
    {
        $this->playlistService->importFromPlaylist($request->input(Fields::ID), $playlistId);

        return $this->success(['message' => 'Playlist imported']);
    }

    public function addFavourite(AddTrackToFavouriteRequest $request)
    {
        $userId = $request->attributes->get(Fields::USER_ID);
        $this->playlistService->addToFavourite($userId, $request->input(Fields::IDS));

        return $this->success(['message' => 'Track added']);
    }

    public function removeFavourite(RemoveTrackFromFavouriteRequest $request)
    {
        $userId = $request->attributes->get(Fields::USER_ID);
        $this->playlistService->removeFromFavourite($userId, $request->input(Fields::IDS));

        return $this->success(['message' => 'Track removed']);
    }
}
