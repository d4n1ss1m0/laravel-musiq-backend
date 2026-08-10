<?php

namespace App\Http\Requests\Playlist;

use App\Models\Playlist;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Rules\TrackInPlaylist;
use App\Shared\Enums\PlaylistTypes;
use App\Shared\Fields\Fields;
use Illuminate\Foundation\Http\FormRequest;

class ChangeTrackOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $maxOrder = (int) TrackPlaylist::query()
            ->whereHas('playlist', function ($query) {
                $query->where('uuid', $this->route('uuid'));
            })
            ->max('order');

        if ($maxOrder < 1) {
            return [
                Fields::ID => [
                    'required',
                    'string',
                    'exists:tracks,uuid',
                ],
                Fields::ORDER => [
                    'required',
                    'integer',
                    function (string $attribute, mixed $value, \Closure $fail): void {
                        $fail('В плейлисте нет треков для изменения порядка.');
                    },
                ],
            ];
        }

        return [
            Fields::ID => [
                'required',
                'string',
                'exists:tracks,uuid',
                new TrackInPlaylist(true, $this->route('uuid'))
            ],
            Fields::ORDER => 'required|integer|min:1|max:'.$maxOrder,
        ];
    }

    public function messages()
    {
        return [
            sprintf("%s.required", Fields::ID) => 'Айди трека обязателен',
            sprintf("%s.string", Fields::ID) => 'Айди трека должно быть строкой',
            sprintf("%s.exists", Fields::ID) => 'Трека с таким айди не существует',

            sprintf("%s.required", Fields::ORDER) => 'Порядкойвый номер обязателен',
            sprintf("%s.integer", Fields::ORDER) => 'Порядкойвый номер должен быть числом',
        ];
    }

}
