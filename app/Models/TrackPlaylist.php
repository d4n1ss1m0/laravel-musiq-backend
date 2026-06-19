<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class TrackPlaylist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'track_id',
        'playlist_id',
        'order'
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

}
