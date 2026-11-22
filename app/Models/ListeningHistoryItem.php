<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $track_id
 * @property string|null $source_type
 * @property int|null $source_id
 * @property Carbon $played_date
 * @property Carbon $last_played_at
 * @property int $play_count
 * @property int $listened_seconds
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ListeningHistoryItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'track_id',
        'source_type',
        'source_id',
        'played_date',
        'last_played_at',
        'play_count',
        'listened_seconds',
    ];

    protected $table = 'listening_history';

    protected $casts = [
        'played_date' => 'date',
        'last_played_at' => 'datetime',
        'play_count' => 'integer',
        'listened_seconds' => 'integer',
    ];

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ListeningAttempt::class, 'history_item_id');
    }
}
