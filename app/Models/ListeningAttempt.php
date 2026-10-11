<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $history_item_id
 * @property int $credited_seconds
 * @property Carbon $started_at
 * @property Carbon|null $last_reported_at
 * @property Carbon|null $finished_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ListeningHistoryItem $historyItem
 */
class ListeningAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'history_item_id',
        'credited_seconds',
        'started_at',
        'last_reported_at',
        'finished_at',
    ];

    protected $casts = [
        'history_item_id' => 'integer',
        'credited_seconds' => 'integer',
        'started_at' => 'datetime',
        'last_reported_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ListeningAttempt $attempt): void {
            $attempt->uuid ??= (string) Str::uuid7();
        });
    }

    public function historyItem(): BelongsTo
    {
        return $this->belongsTo(ListeningHistoryItem::class, 'history_item_id');
    }
}
