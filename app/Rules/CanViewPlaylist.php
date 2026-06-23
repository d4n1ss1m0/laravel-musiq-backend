<?php

namespace App\Rules;

use App\Models\Playlist;
use App\Shared\Enums\PlaylistTypes;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CanViewPlaylist implements ValidationRule
{
    public function __construct(private readonly int $userId)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $canView = Playlist::query()
            ->where('uuid', $value)
            ->where(function ($query) {
                $query
                    ->where('user_id', $this->userId)
                    ->orWhereHas('playlistType', function ($query) {
                        $query->where('name', PlaylistTypes::PUBLIC->value);
                    });
            })
            ->exists();

        if (!$canView) {
            $fail('Плейлист не найден');
        }
    }
}
