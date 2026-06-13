<?php

namespace App\Http\Requests\AddTrack;

use App\Shared\Fields\Fields;
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
            Fields::LINK => 'required|string',
            Fields::SERVICE => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'messages' => [
                sprintf('%s.required', Fields::LINK) => 'Ссылка обязательна для заполнения.',
                sprintf('%s.string', Fields::LINK) => 'Ссылка должна быть строкой.',

                sprintf('%s.required', Fields::SERVICE) => 'Сервис обязателен для заполнения.',
                sprintf('%s.string', Fields::SERVICE) => 'Сервис должен быть строкой.',
            ],
        ];
    }

}
