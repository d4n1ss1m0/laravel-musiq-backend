<?php

namespace App\Http\Controllers\Playlist;


use App\DTO\Playlist\CreatePlaylistDTO;
use App\DTO\Playlist\UpdatePlaylistDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Playlist\AddTrackToPlaylistRequest;
use App\Http\Requests\Playlist\ChangeTrackOrderRequest;
use App\Http\Requests\Playlist\CreatePlaylistRequest;
use App\Http\Requests\Playlist\ImportFromPlaylistRequest;
use App\Http\Requests\Playlist\ManyTracksRequest;
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
        try {
            $playlist = $this->playlistService->getPlaylist($playlistId);
            return new PlaylistResource($playlist);
        } catch (ModelNotFoundException $e) {
            return $this->error($e->getMessage(), 'NotFound', $e->getCode());
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 'error', $e->getCode());
        }

    }

    public function getTracks(string $playlistId, SearchPaginateRequest $request)
    {
        $perPage = $request->query(Fields::PER_PAGE, config('app.per_page_default'));
        $query = $request->query(Fields::QUERY);
        try {
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
        } catch (ModelNotFoundException $e) {
            return $this->error($e->getMessage(), 'NotFound', $e->getCode());
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 'error', $e->getCode());
        }
    }

    public function getQueue(string $playlistId, Request $request)
    {
        try {
            $tracks = $this->playlistService->getQueue($playlistId);
            return $this->success($tracks);
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
                PlaylistTypes::tryFrom($request->type)
            );

            $playlist = $this->playlistService->createPlaylist($dto, $userId);

            return $this->success(new PlaylistResource($playlist));
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 'error', 500);
        }
    }

    public function addTrack(string $playlistId, AddTrackToPlaylistRequest $request)
    {
        try {
            $this->playlistService->addTrackToPlaylist($playlistId, $request->trackId);
            return $this->success(['message' => 'Track added', 'playlistId' => $playlistId, 'trackId' => $request->trackId]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'error', $e->getCode());
        }
    }

    public function removeTrack(string $playlistId, ManyTracksRequest $request)
    {
        try {
            $this->playlistService->removeTrackFromPlaylist($playlistId, $request->trackIds);
            return $this->success(['message' => 'Track removed']);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'error', $e->getCode());
        }

    }

    public function order(string $playlistId, ChangeTrackOrderRequest $request)
    {
        try {
            $this->playlistService->changeOrder($playlistId, $request->trackId, $request->order);
            return $this->success(['message' => 'Order changed']);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'error', $e->getCode());
        }
    }

    public function update(string $playlistId, UpdatePlaylistRequest $request)
    {
        try {
            if ($request->file) {
                $path = $this->fileService->addFile($request->file, 'image/playlist', 'webp');
            }
            $playlistDTO = new UpdatePlaylistDTO(
                $path ?? null,
                $request->name,
                $request->type ? PlaylistTypes::tryFrom($request->type) : null
            );
            $this->playlistService->updatePlaylist($playlistId, $playlistDTO);
            return $this->success(['message' => 'Playlist updated']);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'error', $e->getCode());
        }
    }

    public function delete(string $playlistId)
    {
        try {
            $this->playlistService->deletePlaylist($playlistId);
            return $this->success(['message' => 'Playlist deleted']);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'error', $e->getCode());
        }
    }

    public function importFromPlaylist(string $playlistId, ImportFromPlaylistRequest $request)
    {
        try {
            $this->playlistService->importFromPlaylist($request->input('fromId'), $playlistId);
            return $this->success(['message' => 'Playlist imported']);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'error', $e->getCode());
        }
    }
}
