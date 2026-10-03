<?php

namespace App\Http\Requests\Playback;

use App\Enum\PlaybackManualType;
use App\Shared\Fields\Fields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddToQueueRequest extends FormRequest
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
            Fields::ID => ['required', 'string'],
            Fields::TYPE => ['required', 'string', Rule::enum(PlaybackManualType::class)],
        ];
    }

    public function messages(): array
    {
        return [
            sprintf('%s.required', Fields::ID) => 'Идентификатор трека обязателен для заполнения.',
            sprintf('%s.string', Fields::ID) => 'Идентификатор трека должен быть строкой.',
            sprintf('%s.required', Fields::TYPE) => 'Тип добавления в очередь обязателен для заполнения.',
            sprintf('%s.string', Fields::TYPE) => 'Тип добавления в очередь должен быть строкой.',
            sprintf('%s.enum', Fields::TYPE) => 'Тип добавления в очередь должен иметь значение next или tail.',
        ];
    }
}
