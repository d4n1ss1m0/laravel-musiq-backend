<?php

namespace App\Http\Requests\Player;

use App\Shared\Fields\Fields;
use App\Enums\SearchTypes;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TracksRequest extends FormRequest
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
            Fields::IDS => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'messages' => [
                sprintf('%s.required', Fields::IDS) => 'Необходимо указать id треков',
                sprintf('%s.string', Fields::IDS) => 'Необходимо указать id треков через запятую',
            ],
        ];
    }

}
