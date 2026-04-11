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
            'name' => 'required|string',
            'artists' => 'required|array',
            'artists.*.id' => 'nullable|integer',
            'artists.*.name' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'messages' => [
                'name.required' => 'Название трека обязательно для заполнения.',
                'name.string' => 'Название трека должно быть строкой.',

                'artists.required' => 'Укажите хотя бы одного артиста.',
                'artists.array' => 'Список артистов должен быть массивом.',
                'artists.*.id.integer' => 'Идентификатор артиста должен быть числом.',
                'artists.*.name.required' => 'Имя каждого артиста обязательно.',
                'artists.*.name.string' => 'Имя артиста должно быть строкой.',
            ],
        ];
    }

}
