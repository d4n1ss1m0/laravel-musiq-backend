<?php

namespace App\Repositories\Playlist;



use App\Shared\Enums\PlaylistTypes;

interface PlaylistRepositoryInterface
{
    public function create(string $name, string $file, int $userId, PlaylistTypes $type = PlaylistTypes::PUBLIC);

    public function addTracks(int $playlistId, array $tracks = []);
}
