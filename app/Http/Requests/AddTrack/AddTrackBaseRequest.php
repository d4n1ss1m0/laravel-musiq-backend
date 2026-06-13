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

class AddTrackBaseRequest extends FormRequest
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
            Fields::Name->value => 'required|string',
            Fields::Artists->value => 'required|array',
            sprintf('%s.*.%s', Fields::Artists->value, Fields::Id->value) => 'nullable|integer',
            sprintf('%s.*.%s', Fields::Artists->value, Fields::Name->value) => 'required_without:artists.*.id|string',
        ];
    }

    public function messages()
    {
        return [
            'messages' => [
                sprintf('%s.required', Fields::Name->value) => 'Название трека обязательно для заполнения.',
                sprintf('%s.string', Fields::Name->value)  => 'Название трека должно быть строкой.',

                sprintf('%s.required', Fields::Artists->value)  => 'Укажите хотя бы одного артиста.',
                sprintf('%s.array', Fields::Artists->value)  => 'Список артистов должен быть массивом.',
                sprintf('%s.*.%s.integer', Fields::Artists->value, Fields::Id->value) => 'Идентификатор артиста должен быть числом.',
                sprintf('%s.*.%s.required', Fields::Artists->value, Fields::Name->value)  => 'Имя каждого артиста обязательно.',
                sprintf('%s.*.%s.string', Fields::Artists->value, Fields::Name->value)  => 'Имя артиста должно быть строкой.',
            ],
        ];
    }

}
