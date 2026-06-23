<?php

namespace App\Rules;

use App\Models\Playlist;
use App\Models\TrackPlaylist;
use App\Shared\Enums\PlaylistTypes;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class TrackInPlaylist implements ValidationRule
{
    public function __construct(private readonly bool $shouldExist, private readonly string $playlistId)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $in = TrackPlaylist::query()
            ->whereHas('track', function ($query) use ($value) {
                $query->where('uuid', $value);
            })
            ->whereHas('playlist', function ($query) {
                $query->where('uuid', $this->playlistId);
            })
            ->exists();

        if ($this->shouldExist !== $in) {
            $fail($this->shouldExist
                ? 'Трека нет в плейлисте.'
                : 'Трек уже есть в плейлисте.'
            );
        }
    }
}
