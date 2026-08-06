<?php

namespace App\Enum;

use App\Models\Artist;
use App\Models\Playlist;

enum HistorySource: string
{
    case PLAYLIST = 'playlist';
    case ARTIST = 'artist';

    public function getModel()
    {
        return match ($this) {
            self::PLAYLIST => Playlist::class,
            self::ARTIST => Artist::class,
        };
    }
}
