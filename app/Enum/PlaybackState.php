<?php

namespace App\Enum;

enum PlaybackState: string
{
    case PLAYING = 'playing';
    case PAUSED = 'paused';
    case FINISHED = 'finished';
}
