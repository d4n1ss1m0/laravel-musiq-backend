<?php

namespace App\Models\PlaybackSession;

use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaybackManualQueueItem extends Model
{
    use HasFactory;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'playback_manual_queue';

    protected $fillable = [
        'session_id',
        'track_id',
        'placement',
        'position',
    ];

    protected $casts = [
        'session_id' => 'integer',
        'track_id' => 'integer',
        'position' => 'integer',
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
            ->where('placement', $this->getAttribute('placement'))
            ->where('position', $this->getAttribute('position'));
    }
}
