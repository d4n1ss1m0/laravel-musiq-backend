<?php

namespace App\Models;

use App\Contracts\MediatekaLibraryable;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Playlist extends Model implements MediatekaLibraryable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'image',
        'type',
    ];

    protected static function booted()
    {
        static::creating(function ($playlist) {
            if (!$playlist->id) {
                $playlist->uuid = (string) Str::uuid7();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function playlistType()
    {
        return $this->belongsTo(PlaylistType::class, 'type');
    }

    public function tracks() {
        return $this->belongsToMany(Track::class, 'track_playlists');
    }


}
