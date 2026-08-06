<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property int $user_id
 * @property int $track_id
 * @property string|null $source_type
 * @property int|null $source_id
 * @property \Illuminate\Support\Carbon $played_date
 * @property \Illuminate\Support\Carbon $last_played_at
 * @property int $play_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
        'play_count'
    ];

    protected $table = 'listening_history';

    protected $casts = [
        'played_date' => 'date',
        'last_played_at' => 'datetime',
    ];




}
