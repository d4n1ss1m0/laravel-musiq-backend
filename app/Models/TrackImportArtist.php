<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackImportArtist extends Model
{
    use HasFactory;

    protected $table = 'import_track_artists';

    protected $fillable = [
        'track_id',
        'artist_id',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(TrackImport::class, 'track_id');
    }
}
