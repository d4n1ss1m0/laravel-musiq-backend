<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class RecentlyPlayedTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'track_id',
    ];

}
