<?php

namespace App\Http\Requests\Playback;

use App\Shared\Fields\Fields;
use Illuminate\Foundation\Http\FormRequest;

class PauseRequest extends FormRequest
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
            Fields::SECONDS => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            sprintf('%s.required', Fields::SECONDS) => 'Укажите позицию воспроизведения в секундах.',
            sprintf('%s.integer', Fields::SECONDS) => 'Позиция воспроизведения должна быть целым числом секунд.',
            sprintf('%s.min', Fields::SECONDS) => 'Позиция воспроизведения не может быть отрицательной.',
        ];
    }
}
