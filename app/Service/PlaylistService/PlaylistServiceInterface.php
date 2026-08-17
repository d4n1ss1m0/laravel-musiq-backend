<?php

namespace App\Service\PlaylistService;

use App\DTO\Playlist\CreatePlaylistDTO;
use App\DTO\Playlist\UpdatePlaylistDTO;
use App\Models\Playlist;

interface PlaylistServiceInterface
{
    public function getPlaylist(string $playlistId);
    public function getTracks(string $playlistId, int $perPage, ?string $query = null);
    public function getQueue(string $playlistId);
    public function createPlaylist(CreatePlaylistDTO $dto, int $userId): Playlist;
    public function addTrackToPlaylist(string $playlistId, array $trackIds) : void;
    public function removeTrackFromPlaylist(string $playlistId, array $trackId) : void;
    public function changeOrder(string $playlistId, string $trackId, int $order);
    public function updatePlaylist(string $playlistId, UpdatePlaylistDTO $dto);
    public function deletePlaylist(string $playlistId);
    public function importFromPlaylist(string $fromId, string $toId);

    public function getFavouritePlaylist(int $userId): Playlist;
    public function addToFavourite(int $userId, array $trackIds) : void;
    public function removeFromFavourite(int $userId, array $trackIds) : void;
}
