<?php

namespace App\Enum;

use App\Contracts\MediatekaLibraryable;
use App\Models\Artist;
use App\Models\Playlist;

enum MediatekaItemType: string
{
    case PLAYLIST = 'playlist';
    case ARTIST = 'artist';


    /**
     * @return class-string<MediatekaLibraryable>
     */
    public function getModel(): string
    {
        return match ($this) {
            self::PLAYLIST => Playlist::class,
            self::ARTIST => Artist::class,
        };
    }
}
