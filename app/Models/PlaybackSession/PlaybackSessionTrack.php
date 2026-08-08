<?php

namespace App\Models\PlaybackSession;

use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaybackSessionTrack extends Model
{
    use HasFactory;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'track_id',
        'source_position',
        'playback_position',
    ];

    protected $casts = [
        'session_id' => 'integer',
        'track_id' => 'integer',
        'source_position' => 'integer',
        'playback_position' => 'integer',
    ];

    public function session()
    {
        return $this->belongsTo(PlaybackSession::class, 'session_id');
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('session_id', $this->getAttribute('session_id'))
            ->where('source_position', $this->getAttribute('source_position'));
    }
}
