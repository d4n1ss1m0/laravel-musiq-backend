<?php

namespace App\Http\Requests\Playlist;

use App\Models\Playlist;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Rules\TrackInPlaylist;
use App\Shared\Enums\PlaylistTypes;
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
            'trackId' => [
                'required',
                'string',
                'exists:tracks,uuid',
                new TrackInPlaylist(false, $this->route('uuid'))
            ]
            ];
    }

    public function messages()
    {
        return [
            'trackId.required' => 'Айди трека обязателен',
            'trackId.string' => 'Айди трека должно быть строкой'
        ];
    }

}
