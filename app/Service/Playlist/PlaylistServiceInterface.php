<?php
namespace App\Service\Playlist;
interface PlaylistServiceInterface {

    public function getPlaylist(int $playlistId);
    public function getTracks(int $playlistId);

    public function editPlaylist(int $playlistId);

    public function deletePlaylist(int $playlistId);
}
