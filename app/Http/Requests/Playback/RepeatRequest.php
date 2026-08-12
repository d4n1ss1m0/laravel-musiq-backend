<?php

namespace App\Http\Requests\Playback;

use App\Enum\RepeatType;
use App\Shared\Fields\Fields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RepeatRequest extends FormRequest
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
            Fields::REPEAT => [
                'required',
                'string',
                Rule::enum(RepeatType::class)
            ],
        ];
    }

    public function messages()
    {
        return [
            'messages' => [
                sprintf('%s.required', Fields::REPEAT) => 'Тип повтора обязателен',
                sprintf('%s.string', Fields::REPEAT)  => 'Тип повтора должен быть строкой',
            ],
        ];
    }

}
