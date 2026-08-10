<?php

namespace App\Http\Requests\Playlist;

use App\Models\Playlist;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Rules\TrackInPlaylist;
use App\Shared\Enums\PlaylistTypes;
use App\Shared\Fields\Fields;
use Illuminate\Foundation\Http\FormRequest;

class AddTrackToPlaylistRequest extends FormRequest
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
        return [
            Fields::IDS => [
                'required',
                'array',
            ],
            Fields::IDS . '.*' => [
                'distinct',
                'required',
                'string',
                'exists:tracks,uuid',
                new TrackInPlaylist(false, $this->route('uuid')),
            ],
        ];
    }

    public function messages()
    {
        return [
            sprintf('%s.required', Fields::IDS) => 'Масссив треков обязателен',
            sprintf('%s.array', Fields::IDS) => 'Масссив треков обязателен',
            sprintf('%s.*.required', Fields::IDS) => 'Айди трека обязателен',
            sprintf('%s.*.string', Fields::IDS) => 'Айди трека должен быть строкой',
            sprintf('%s.*.exists', Fields::IDS) => 'Трек с таким айди не существует',
        ];
    }

}
