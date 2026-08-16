<?php

namespace App\Rules;

use App\Models\Playlist;
use App\Models\TrackPlaylist;
use App\Shared\Enums\PlaylistTypes;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class TrackInFavourite implements ValidationRule
{
    public function __construct(private readonly bool $shouldExist, private readonly int $userId)
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
                $query->where('user_id', $this->userId)
                    ->where('type', PlaylistTypes::FAVOURITE->getId());
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
