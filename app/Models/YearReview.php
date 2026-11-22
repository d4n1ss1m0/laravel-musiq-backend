<?php

namespace App\Models;

use App\Models\Auth\User;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $year
 * @property array $review
 * @property string $status
 * @property DateTime $available_from
 * @property bool $viewed
 * @property-read User $user
 */
class YearReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'review',
        'status',
        'available_from',
        'viewed',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'year' => 'integer',
        'review' => 'array',
        'available_from' => 'datetime',
        'viewed' => 'boolean',
    ];

    public function scopeAvailableTo(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId)
            ->where('status', 'success')
            ->where('available_from', '<=', now());
    }

    public function getReviewAttribute(?string $value): ?array
    {
        if ($value === null) {
            return null;
        }

        $review = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

        // Older jobs stored a JSON string inside the JSON column.
        if (is_string($review)) {
            $review = json_decode($review, true, 512, JSON_THROW_ON_ERROR);
        }

        return $review;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
