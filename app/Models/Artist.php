<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Artist extends Model
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
    ];

    public function tracks() {
        return $this->belongsToMany(Track::class, 'track_artists');
    }

    public function favouriteCount() {
        return FavouriteArtist::where('artist_id', $this->id)->count();
    }

    public function users() {
        return $this->belongsToMany(User::class, 'favourite_artists');
    }


}
