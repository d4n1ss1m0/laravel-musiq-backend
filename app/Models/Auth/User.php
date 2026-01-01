<?php

namespace App\Models\Auth;

use App\Models\Playlist;
use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function playlists() {
        return $this->hasMany(Playlist::class);
    }

    public function recentlyPlayedTracks() {
        return $this->belongsToMany(Track::class, 'recently_played_tracks');
    }

    public function recentlyPlayedPlaylists() {
        return $this->belongsToMany(Playlist::class, 'recently_played_playlists');
    }

    public function checkPassword(string $password):bool
    {
        return password_verify($password, $this->password);
    }


}
