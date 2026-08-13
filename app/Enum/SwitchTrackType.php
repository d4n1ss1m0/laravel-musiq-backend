<?php

namespace App\Enum;

enum SwitchTrackType: string
{
    case NEXT = 'next';
    case PREVIOUS = 'previous';

    public function getOrder()
    {
        return match ($this) {
            self::PREVIOUS => 'desc',
            self::NEXT => 'asc',
        };
    }

    public function getOffsetSign()
    {
        return match ($this) {
            self::PREVIOUS => '<',
            self::NEXT => '>',
        };
    }
}
