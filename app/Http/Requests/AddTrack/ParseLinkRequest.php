<?php

namespace App\Http\Requests\AddTrack;

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
            'link' => 'required|string',
            'service' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'messages' => [
                'link.required' => 'Ссылка обязательна для заполнения.',
                'link.string' => 'Ссылка должна быть строкой.',

                'service.required' => 'Сервис обязателен для заполнения.',
                'service.string' => 'Сервис должен быть строкой.',
            ],
        ];
    }

}
