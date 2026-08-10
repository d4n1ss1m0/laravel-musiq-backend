<?php

namespace App\Http\Requests\Playlist;

use App\Models\Playlist;
use App\Rules\CanViewPlaylist;
use App\Shared\Enums\PlaylistTypes;
use App\Shared\Fields\Fields;
use Illuminate\Foundation\Http\FormRequest;

class ImportFromPlaylistRequest extends FormRequest
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
            Fields::ID => [
                'required',
                'uuid',
                'exists:playlists,uuid',
                new CanViewPlaylist($this->attributes->get('userId')),
            ],
        ];
    }

    public function messages()
    {
        return [
            sprintf('%s.required', Fields::ID) => 'Айди обязателен',
            sprintf('%s.string', Fields::ID) => 'Айди строка',
            sprintf('%s.exists', Fields::ID) => 'Плейлиста не существует'
        ];
    }

}
