<?php

namespace App\Repositories\Playlist;

use App\Models\Playlist;
use App\Models\PlaylistType;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Shared\Enums\PlaylistTypes;
use Illuminate\Support\Facades\DB;

class PlaylistRepository implements PlaylistRepositoryInterface
{
    public function create(string $name, string $file, int $userId, PlaylistTypes $type = PlaylistTypes::PUBLIC)
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
        $data = [];
        foreach ($tracks as $track) {
            $data[] = [
                'playlist_id' => $playlistId,
                'track_id' => $track->id,
                'order' => $track->order
            ];
        }

        TrackPlaylist::insert($data);
    }

    public function updateOrder(int $playlistId, array $trackIds) {
        $values = [];
        $bindings = [];

        foreach ($trackIds as $index => $trackId) {
            $values[] = '(?, ?)';
            $bindings[] = $trackId;
            $bindings[] = $index + 1;
        }

        $bindings[] = $playlistId;

        DB::update('
            UPDATE track_playlists AS tp
            SET "order" = v.order_value::integer
            FROM tracks AS t
            JOIN (VALUES ' . implode(', ', $values) . ') AS v(track_uuid, order_value)
              ON t.uuid = v.track_uuid::uuid
            WHERE tp.track_id = t.id
              AND tp.playlist_id = ?
            ',
            $bindings
        );

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
}
