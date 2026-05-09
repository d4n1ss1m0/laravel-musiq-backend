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

class AddTrackByLinkRequest extends AddTrackBaseRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'cover' => 'required_without:coverName|file|mimes:jpg,png,webp',
            'coverName' => 'required_without:cover|string|max:255',
            'file' => 'required|string|max:255'
        ]);
    }

    public function messages()
    {
        $messages = [
            'cover.file' => 'Обложка должна быть файлом.',
            'cover.mimes' => 'Обложка должна быть в формате JPG или PNG.',
            'cover.required_without' => 'Файл обязателен, если нет временного файла',
            'coverName.required_without' => 'Название временного файла обязательно, если нет файла'
        ];

        $parent = parent::messages();
        $parent['messages'] = array_merge($parent['messages'], $messages);

        return $parent;
    }

}
