<?php

namespace App\Http\Requests\Playlist;

use App\Models\Playlist;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Rules\TrackInFavourite;
use App\Rules\TrackInPlaylist;
use App\Shared\Enums\PlaylistTypes;
use App\Shared\Fields\Fields;
use Illuminate\Foundation\Http\FormRequest;

class MostPlayablePlaylistsRequest extends FormRequest
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
            Fields::PER_PAGE => 'required|integer|min:1|max:10',
        ];
    }

    public function messages()
    {
        return [
            sprintf('%s.required', Fields::PER_PAGE) => 'Количество элементов на странице обязательно.',
            sprintf('%s.integer', Fields::PER_PAGE) => 'Количество элементов на странице должно быть целым числом.',
            sprintf('%s.min', Fields::PER_PAGE) => 'Количество элементов на странице должно быть не меньше 1.',
            sprintf('%s.max', Fields::PER_PAGE) => 'Количество элементов на странице должно быть не больше 10.',
        ];
    }

}
