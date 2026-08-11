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
            Fields::NAME => 'required|string',
            Fields::ARTISTS => 'required|array',
            sprintf('%s.*.%s', Fields::ARTISTS, Fields::ID) => [
                'nullable',
                'string',
                'exists:artists,uuid',
                sprintf('required_without:%s.*.%s', Fields::ARTISTS, Fields::NAME),
            ],
            sprintf('%s.*.%s', Fields::ARTISTS, Fields::NAME) => [
                'nullable',
                'string',
                sprintf('required_without:%s.*.%s', Fields::ARTISTS, Fields::ID),
            ],
        ];
    }

    public function messages()
    {
        return [
            'messages' => [
                sprintf('%s.required', Fields::NAME) => 'Название трека обязательно для заполнения.',
                sprintf('%s.string', Fields::NAME)  => 'Название трека должно быть строкой.',

                sprintf('%s.required', Fields::ARTISTS)  => 'Укажите хотя бы одного артиста.',
                sprintf('%s.array', Fields::ARTISTS)  => 'Список артистов должен быть массивом.',
                sprintf('%s.*.%s.string', Fields::ARTISTS, Fields::ID) => 'Идентификатор артиста должен быть строкой.',
                sprintf('%s.*.%s.required', Fields::ARTISTS, Fields::NAME)  => 'Имя каждого артиста обязательно.',
                sprintf('%s.*.%s.string', Fields::ARTISTS, Fields::NAME)  => 'Имя артиста должно быть строкой.',
            ],
        ];
    }

}
