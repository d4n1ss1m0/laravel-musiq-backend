<?php

namespace App\Enum;

enum RepeatType: string
{
    case OFF = 'off';
    case TRACK = 'track';
    case QUEUE = 'queue';
}
