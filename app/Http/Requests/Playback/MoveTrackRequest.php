<?php

namespace App\Http\Requests\Playback;

use App\Shared\Fields\Fields;
use Illuminate\Foundation\Http\FormRequest;

class MoveTrackRequest extends FormRequest
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
            Fields::POSITION => ['required', 'integer', 'min:1'],
            Fields::CURRENT_POSITION => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            sprintf('%s.required', Fields::POSITION) => 'Укажите новую позицию трека.',
            sprintf('%s.integer', Fields::POSITION) => 'Новая позиция трека должна быть целым числом.',
            sprintf('%s.min', Fields::POSITION) => 'Новая позиция трека должна быть не меньше 1.',
            sprintf('%s.required', Fields::CURRENT_POSITION) => 'Укажите текущую позицию трека.',
            sprintf('%s.integer', Fields::CURRENT_POSITION) => 'Текущая позиция трека должна быть целым числом.',
            sprintf('%s.min', Fields::CURRENT_POSITION) => 'Текущая позиция трека должна быть не меньше 1.',
        ];
    }
}
