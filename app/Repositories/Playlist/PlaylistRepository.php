<?php

namespace App\Repositories\Playlist;

use App\Enum\PlaylistOrderType;
use App\Exceptions\NotFoundException;
use App\Models\Playlist;
use App\Models\PlaylistType;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Shared\Enums\PlaylistTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlaylistRepository implements PlaylistRepositoryInterface
{
    public function getByUUID(string $uuid): ?Playlist
    {
        return Playlist::query()
            ->where('uuid', $uuid)
            ->first();
    }

    public function create(string $name, string|null $file, int $userId, PlaylistTypes $type = PlaylistTypes::PUBLIC)
    {
        $typeInt = PlaylistType::where('name', $type->value)->value('id');

        return Playlist::create([
            'user_id' => $userId,
            'name' => $name,
            'image' => $file,
            'type' => $typeInt
        ]);
    }

    public function addTracks(int $playlistId, array $tracks = [])
    {
        $now = Carbon::now();
        $data = [];
        foreach ($tracks as $track) {
            $data[] = [
                'playlist_id' => $playlistId,
                'track_id' => $track->id,
                'order' => $track->order,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        TrackPlaylist::insert($data);
    }

    public function updateOrder(int $playlistId, int $trackId, int $order)
    {
        DB::transaction(function () use ($playlistId, $trackId, $order) {
            try {
                $item = TrackPlaylist::query()
                    ->where('playlist_id', $playlistId)
                    ->where('track_id', $trackId)
                    ->lockForUpdate()
                    ->firstOrFail();
            } catch (ModelNotFoundException $e) {
                throw new NotFoundException('Track not found');
            }


            $currentOrder = $item->order;

            if ($currentOrder === $order) {
                return;
            }

            if ($order < $currentOrder) {
                TrackPlaylist::query()
                    ->where('playlist_id', $playlistId)
                    ->where('order', '>=', $order)
                    ->where('order', '<', $currentOrder)
                    ->update(['order' => DB::raw('"order" + 1')]);
            } else {
                TrackPlaylist::query()
                    ->where('playlist_id', $playlistId)
                    ->where('order', '>', $currentOrder)
                    ->where('order', '<=', $order)
                    ->update(['order' => DB::raw('"order" - 1')]);
            }

            $item->update(['order' => $order]);
        });
    }

    public function removeTracks(int $playlistId, array $tracks = [])
    {
        $trackIds = Track::query()
            ->whereIn('uuid', $tracks)
            ->pluck('id');

        DB::transaction(function () use ($playlistId, $trackIds) {
            TrackPlaylist::query()
                ->whereIn('track_id', $trackIds)
                ->where('playlist_id', $playlistId)
                ->delete();

            DB::statement(
                '
            WITH ordered AS (
                SELECT
                    id,
                    row_number() OVER (ORDER BY "order") AS new_order
                FROM track_playlists
                WHERE playlist_id = ?
            )
            UPDATE track_playlists AS tp
            SET "order" = ordered.new_order
            FROM ordered
            WHERE tp.id = ordered.id
            ',
                [$playlistId]
            );
        });
    }

    public function update(int $playlistId, ?string $name = null, ?string $file = null, ?PlaylistTypes $type = null)
    {
        $data = [];
        if ($name) {
            $data['name'] = $name;
        }
        if ($file) {
            $data['image'] = $file;
        }
        if ($type) {
            $data['type'] = $type->getId();
        }

        Playlist::query()
            ->where('id', $playlistId)
            ->update($data);
    }

    public function getPlaylistTracks(string $uuid, PlaylistOrderType $order = PlaylistOrderType::POSITION): Builder
    {
        $query = Track::query()
            ->select('tracks.*')
            ->join('track_playlists', 'track_playlists.track_id', '=', 'tracks.id')
            ->join('playlists', 'playlists.id', '=', 'track_playlists.playlist_id')
            ->where('playlists.uuid', $uuid);

        match ($order) {
            PlaylistOrderType::POSITION => $query->orderBy('track_playlists.order'),
            PlaylistOrderType::ARTIST => $query->orderBy(
                DB::table('track_artists')
                    ->join('artists', 'artists.id', '=', 'track_artists.artist_id')
                    ->select('artists.name')
                    ->whereColumn('track_artists.track_id', 'tracks.id')
                    ->orderBy('track_artists.id')
                    ->limit(1)
            ),
            PlaylistOrderType::TITLE => $query->orderBy('tracks.name'),
            PlaylistOrderType::ADDED => $query->orderByDesc('track_playlists.created_at'),
        };

        return $query->orderBy('track_playlists.id', $order === PlaylistOrderType::ADDED ? 'desc' : 'asc');
    }
}
