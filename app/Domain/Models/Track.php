<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Track extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'time',
        'text',
        'song',
        'image',
        'is_private',
        'user_id',
    ];

    public function artists() {
        return $this->belongsToMany(Artist::class, 'track_artists');
    }


}
