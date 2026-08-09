<?php

namespace App\Models\PlaybackSession;

use App\Enum\PlaybackManualType;
use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $session_id
 * @property int $track_id
 * @property PlaybackManualType $placement
 * @property int $position
 * @property-read PlaybackSession $session
 * @property-read Track $track
 */
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
        'source_position',
        'playback_position',
    ];

    protected $casts = [
        'session_id' => 'integer',
        'track_id' => 'integer',
        'source_position' => 'integer',
        'playback_position' => 'integer',
        'placement' => PlaybackManualType::class,
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(PlaybackSession::class, 'session_id');
    }

    public function track(): BelongsTo
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
