<?php

namespace App\Http\Requests\Playlist;

use App\Models\Playlist;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Rules\TrackInPlaylist;
use App\Shared\Enums\PlaylistTypes;
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
        $maxOrder = TrackPlaylist::query()
            ->whereHas('playlist', function ($query) {
                $query->where('uuid', $this->route('uuid'));
            })
            ->max('order');

        return [
            'trackId' => [
                'required',
                'string',
                'exists:tracks,uuid',
                new TrackInPlaylist(true, $this->route('uuid'))
            ],
            'order' => 'required|integer|min:1|max:'.$maxOrder,
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
