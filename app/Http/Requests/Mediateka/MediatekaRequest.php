<?php

namespace App\Http\Requests\Mediateka;

use App\Enum\OrderBy;
use App\Shared\Fields\Fields;
use App\Enums\SearchTypes;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MediatekaRequest extends FormRequest
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
            Fields::ORDER => [
                'required',
                'string',
                'default' => OrderBy::CREATED_AT,
                Rule::enum(OrderBy::class)
            ]
        ];
    }

    public function messages()
    {
        return [
            sprintf('%s.required', Fields::ID) => 'Необходимо указать id медиа',
            sprintf('%s.string', Fields::ID) => 'Необходимо указать id медиа в виде строки',
        ];
    }

}
