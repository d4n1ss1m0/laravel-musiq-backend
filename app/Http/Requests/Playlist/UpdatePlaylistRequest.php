<?php

namespace App\Http\Requests\Playlist;

use App\Shared\Enums\PlaylistTypes;
use App\Shared\Fields\Fields;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlaylistRequest extends FormRequest
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
            Fields::FILE => 'sometimes|file|mimes:jpg,png,webp',
            Fields::NAME => 'sometimes|string',
            Fields::TYPE => 'sometimes|string|in:'. implode(',', [PlaylistTypes::PRIVATE->value, PlaylistTypes::PUBLIC->value]),
        ];
    }

    public function messages()
    {
        return [];
    }

}
