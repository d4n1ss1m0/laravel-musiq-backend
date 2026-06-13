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

class AddTrackByLinkRequest extends AddTrackBaseRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            Fields::Cover->value => 'required_without:coverName|file|mimes:jpg,png,webp',
            Fields::CoverName->value  => 'required_without:cover|string|max:255',
            Fields::File->value  => 'required|string|max:255'
        ]);
    }

    public function messages()
    {
        $messages = [
            sprintf('%s.file', Fields::Cover->value) => 'Обложка должна быть файлом.',
            sprintf('%s.mimes', Fields::Cover->value) => 'Обложка должна быть в формате JPG или PNG.',
            sprintf('%s.required_without', Fields::Cover->value) => 'Файл обязателен, если нет временного файла',
            sprintf('%s.required_without', Fields::CoverName->value) => 'Название временного файла обязательно, если нет файла'
        ];

        $parent = parent::messages();
        $parent['messages'] = array_merge($parent['messages'], $messages);

        return $parent;
    }

}
