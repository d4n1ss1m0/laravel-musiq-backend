<?php

namespace App\Repositories\Playlist;



use App\Models\Playlist;
use App\Shared\Enums\PlaylistTypes;

interface PlaylistRepositoryInterface
{
    public function getByUUID(string $uuid): ?Playlist;
    public function create(string $name, string|null $file, int $userId, PlaylistTypes $type = PlaylistTypes::PUBLIC);
    public function update(int $playlistId, string|null $name = null, string|null $file = null, PlaylistTypes|null $type = null);

    public function addTracks(int $playlistId, array $tracks = []);
    public function removeTracks(int $playlistId, array $tracks = []);

    public function updateOrder(int $playlistId, int $trackId, int $order);
}
