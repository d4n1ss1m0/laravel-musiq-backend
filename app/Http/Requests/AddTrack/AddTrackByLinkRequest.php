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

class AddTrackByLinkRequest extends AddTrackBaseRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            Fields::COVER => 'required_without:coverName|file|mimes:jpg,png,webp',
            Fields::COVER_NAME  => 'required_without:cover|string|max:255',
            Fields::FILE  => 'required|string|max:255'
        ]);
    }

    public function messages()
    {
        $messages = [
            sprintf('%s.file', Fields::COVER) => 'Обложка должна быть файлом.',
            sprintf('%s.mimes', Fields::COVER) => 'Обложка должна быть в формате JPG или PNG.',
            sprintf('%s.required_without', Fields::COVER) => 'Файл обязателен, если нет временного файла',
            sprintf('%s.required_without', Fields::COVER_NAME) => 'Название временного файла обязательно, если нет файла'
        ];

        $parent = parent::messages();
        $parent['messages'] = array_merge($parent['messages'], $messages);

        return $parent;
    }

}
