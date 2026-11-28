<?php

namespace App\Models;

use App\Enum\PlaylistOrderType;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPlaylistSetting extends Model
{
    protected $fillable = [
        'user_id',
        'playlist_id',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'sort' => PlaylistOrderType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class);
    }
}
