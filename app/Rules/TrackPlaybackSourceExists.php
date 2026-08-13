<?php

namespace App\Rules;

use App\Enum\PlaybackSource;
use App\Models\Playlist;
use App\Models\TrackPlaylist;
use App\Shared\Enums\PlaylistTypes;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class TrackPlaybackSourceExists implements ValidationRule
{
    public function __construct(private readonly bool $shouldExist, private readonly PlaybackSource $source, private readonly string $sourceId)
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
        $source = $model::query()
            ->where('uuid', $this->sourceId)
            ->first();

        if ($source === null && $this->source != PlaybackSource::TRACK) {
            $fail('Источник не существует');
            return;
        }

        if ($this->source == PlaybackSource::TRACK) {
            $in = !is_null($source) && $this->sourceId == $value;
        } else {
            $in = $source->tracks()->where('uuid', $value)->exists();
        }

        if ($this->shouldExist !== $in) {
            $fail($this->shouldExist
                ? 'Трека не существует'
                : 'Трек существует'
            );
        }
    }
}
