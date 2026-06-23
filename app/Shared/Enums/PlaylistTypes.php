<?php


namespace App\Shared\Enums;

use App\Models\PlaylistType;

enum PlaylistTypes: string
{
    case FAVOURITE = 'favourite';
    case PUBLIC = 'public';
    case PRIVATE = 'private';

    public function getId() {
        return PlaylistType::where('name', $this->value)->value('id');
    }



}
