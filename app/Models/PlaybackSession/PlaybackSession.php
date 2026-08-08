<?php

namespace App\Models\PlaybackSession;

use App\Models\Auth\User;
use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaybackSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'source_type',
        'source_id',
        'current_track_id',
        'current_position',
        'shuffle',
        'repeat_mode',
        'state',
    ];

    protected $casts = [
        'current_position' => 'integer',
        'shuffle' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function currentTrack()
    {
        return $this->belongsTo(Track::class, 'current_track_id');
    }

    public function sessionTracks()
    {
        return $this->hasMany(PlaybackSessionTrack::class, 'session_id')
            ->orderBy('playback_position');
    }

    public function manualQueueItems()
    {
        return $this->hasMany(PlaybackManualQueueItem::class, 'session_id')
            ->orderBy('placement')
            ->orderBy('position');
    }
}
