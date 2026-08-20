<?php

namespace App\Models;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrackImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'text',
        'song',
        'image',
        'is_private',
        'user_id',
        'status'
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'import_track_artists', 'track_id', 'artist_id')->withPivot('id');
    }

    public function trackArtists(): HasMany
    {
        return $this->hasMany(TrackImportArtist::class, 'track_id');
    }
}
