<?php

namespace App\Rules;

use App\Enum\PlaybackSource;
use App\Models\Playlist;
use App\Models\TrackPlaylist;
use App\Shared\Enums\PlaylistTypes;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PlaybackSourceExists implements ValidationRule
{
    public function __construct(private readonly bool $shouldExist, private readonly PlaybackSource $source)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $model = $this->source->getModel();
        $in = $model::query()
            ->where('uuid', $value)
            ->exists();

        if ($this->shouldExist !== $in) {
            $fail($this->shouldExist
                ? 'Источник не существует'
                : 'Источник существует'
            );
        }
    }
}
