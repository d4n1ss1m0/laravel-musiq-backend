<?php

namespace App\Enum;

enum PlaylistOrderType: string
{
    case POSITION = 'position';
    case ARTIST = 'artist';
    case TITLE = 'title';
    case ADDED = 'added';

    public function getOrderDirection()
    {
        return match ($this) {
            self::POSITION, self::ARTIST, self::TITLE  => 'asc',
            self::ADDED => 'desc',
        };
    }
}
