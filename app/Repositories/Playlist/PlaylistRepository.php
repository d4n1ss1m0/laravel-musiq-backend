<?php

namespace App\Repositories\Playlist;

use App\Models\Playlist;
use App\Models\PlaylistType;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Shared\Enums\PlaylistTypes;

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
                'track_id' => $track
            ];
        }

        TrackPlaylist::insert($data);
    }
}
