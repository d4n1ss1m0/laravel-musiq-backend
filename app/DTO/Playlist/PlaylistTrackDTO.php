<?php

namespace App\DTO\Playlist;

class PlaylistTrackDTO
{
    public string $id;
    public int $order;

    public function __construct(string $id, int $order)
    {
        $this->id = $id;
        $this->order = $order;
    }
}
