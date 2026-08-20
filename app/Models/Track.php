<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Track extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected static function booted()
    {
        static::creating(function ($track) {
            if (!$track->id) {
                $track->uuid = (string) Str::uuid7();
            }
        });
    }

    protected $fillable = [
        'name',
        'time',
        'text',
        'song',
        'image',
        'is_private',
        'user_id',
        'integrated_lufs',
        'true_peak_db',
    ];

    public function artists() {
        return $this->belongsToMany(Artist::class, 'track_artists')->orderByPivot('id');
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'track_playlists');
    }


}
