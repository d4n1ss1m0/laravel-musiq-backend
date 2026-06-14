<?php

namespace App\Service\PlaylistService;

use App\DTO\CreatePlaylistDTO;
use App\Models\Playlist;

interface PlaylistServiceInterface
{
    public function getPlaylist(int $playlistId, int $userId);
    public function getTracks(int $playlistId, int $userId);
    public function createPlaylist(CreatePlaylistDTO $dto, int $userId): Playlist;
    public function addTrackToPlaylist(int $playlistId, string $trackId) : void;
    public function removeTrackFromPlaylist(int $playlistId, array $trackId) : void;
    public function changeOrder(int $playlistId, array $tracks);
}
