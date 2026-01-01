<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackArtists extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'track_id',
        'artist_id'
    ];

    public function artist() {
        return $this->belongsTo(Artist::class);
    }

    public function track() {
        return $this->belongsTo(Track::class);
    }




}
