<?php

namespace App\Models\PlaybackSession;

use App\Enum\PlaybackState;
use App\Enum\RepeatType;
use App\Models\Auth\User;
use App\Models\Track;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $source_type
 * @property int|null $source_id
 * @property int|null $current_track_id
 * @property int $current_position
 * @property bool $shuffle
 * @property RepeatType $repeat_mode
 * @property PlaybackState $state
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Model|null $source
 * @property-read Track|null $currentTrack
 * @property Collection<int, PlaybackSessionTrack> $sessionTracks
 * @property-read Collection<int, PlaybackManualQueueItem> $manualQueueItems
 */
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
        'repeat_mode' => RepeatType::class,
        'state' => PlaybackState::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function currentTrack(): BelongsTo
    {
        return $this->belongsTo(Track::class, 'current_track_id');
    }

    public function sessionTracks(): HasMany
    {
        return $this->hasMany(PlaybackSessionTrack::class, 'session_id')
            ->orderBy('playback_position');
    }

    public function manualQueueItems(): HasMany
    {
        return $this->hasMany(PlaybackManualQueueItem::class, 'session_id')
            ->orderBy('placement')
            ->orderBy('position');
    }

    public function nextQueueItem(): ?PlaybackSessionTrack
    {
        $tracks = $this->sessionTracks;

        $nextTrack = $tracks
            ->where('playback_position', '>', $this->current_position)
            ->sortBy('playback_position')
            ->first();

        if (!$nextTrack && $this->repeat_mode === RepeatType::QUEUE) {
            return $tracks
                ->sortBy('playback_position')
                ->first();
        }

        return $nextTrack;
    }

    public function previousQueueItem(): ?PlaybackSessionTrack
    {
        $tracks = $this->sessionTracks;

        $previousTrack = $tracks
            ->where('playback_position', '<', $this->current_position)
            ->sortByDesc('playback_position')
            ->first();

        if (!$previousTrack && $this->repeat_mode === RepeatType::QUEUE) {
            return $tracks
                ->sortByDesc('playback_position')
                ->first();
        }

        return $previousTrack;
    }
}
