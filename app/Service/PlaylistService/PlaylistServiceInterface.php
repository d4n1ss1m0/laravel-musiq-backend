<?php

namespace App\Service\PlaylistService;

use App\DTO\Playlist\CreatePlaylistDTO;
use App\DTO\Playlist\UpdatePlaylistDTO;
use App\Enum\PlaylistOrderType;
use App\Models\Playlist;

interface PlaylistServiceInterface
{
    public function getPlaylist(string $playlistId, ?int $userId = null);
    public function getTracks(string $playlistId, int $userId, int $perPage, ?string $query = null);
    public function getQueue(string $playlistId, int $userId);
    public function createPlaylist(CreatePlaylistDTO $dto, int $userId): Playlist;
    public function addTrackToPlaylist(string $playlistId, array $trackIds) : void;
    public function removeTrackFromPlaylist(string $playlistId, array $trackId) : void;
    public function changeOrder(string $playlistId, string $trackId, int $order);
    public function updatePlaylist(string $playlistId, UpdatePlaylistDTO $dto);
    public function deletePlaylist(string $playlistId);
    public function importFromPlaylist(string $fromId, string $toId);

    public function addToFavourite(int $userId, array $trackIds): void;
    public function removeFromFavourite(int $userId, array $trackIds): void;

    public function saveSort(int $userId, string $playlistId, PlaylistOrderType $sort): void;
}
