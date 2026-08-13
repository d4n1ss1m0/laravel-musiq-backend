<?php

namespace App\Models;

use App\Contracts\MediatekaLibraryable;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Artist extends Model implements MediatekaLibraryable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image',
        'uuid',
    ];

    protected static function booted()
    {
        static::creating(function ($artist) {
            if (!$artist->uuid) {
                $artist->uuid = (string) Str::uuid7();
            }
        });
    }

    public function tracks() {
        return $this->belongsToMany(Track::class, 'track_artists')->withPivot('id');
    }

    public function favouriteCount() {
        return FavouriteArtist::where('artist_id', $this->id)->count();
    }

    public function users() {
        return $this->belongsToMany(User::class, 'favourite_artists');
    }


}
