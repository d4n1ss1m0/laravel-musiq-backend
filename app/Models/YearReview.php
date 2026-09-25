<?php

namespace App\Models;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $year
 * @property array $review
 * @property string $status
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
    ];

    protected $casts = [
        'user_id' => 'integer',
        'year' => 'integer',
        'review' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
