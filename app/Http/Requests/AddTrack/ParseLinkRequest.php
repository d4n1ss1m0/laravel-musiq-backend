<?php

namespace App\Http\Requests\AddTrack;

use App\Enum\Fields\Fields;
use App\Enums\SearchTypes;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ParseLinkRequest extends FormRequest
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
            Fields::Link->value => 'required|string',
            Fields::Service->value => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'messages' => [
                sprintf('%s.required', Fields::Link->value) => 'Ссылка обязательна для заполнения.',
                sprintf('%s.string', Fields::Link->value) => 'Ссылка должна быть строкой.',

                sprintf('%s.required', Fields::Service->value) => 'Сервис обязателен для заполнения.',
                sprintf('%s.string', Fields::Service->value) => 'Сервис должен быть строкой.',
            ],
        ];
    }

}
