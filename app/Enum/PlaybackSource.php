<?php

namespace App\Enum;

use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Track;

enum PlaybackSource: string
{
    case PLAYLIST = 'playlist';
    case ARTIST = 'artist';
    case TRACK = 'track';
//    case album = 'album';

    public function getModel()
    {
        return match ($this) {
            self::PLAYLIST => Playlist::class,
            self::ARTIST => Artist::class,
            self::TRACK => Track::class,
        };
    }
}
