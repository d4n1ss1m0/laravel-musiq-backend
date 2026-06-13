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

class AddTrackByFileRequest extends AddTrackBaseRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            Fields::FILE => 'required|file|mimes:mp3,flac,wav',
            Fields::COVER  => 'required|file|mimes:jpg,png,webp'
        ]);
    }

    public function messages()
    {
        $messages = [
            sprintf('%s.required', Fields::FILE) => 'Пожалуйста, загрузите аудиофайл.',
            sprintf('%s.file', Fields::FILE) => 'Файл должен быть корректным.',
            sprintf('%s.mimes', Fields::FILE) => 'Аудиофайл должен быть в формате MP3 или FLAC.',

            sprintf('%s.required', Fields::COVER) => 'Пожалуйста, загрузите обложку.',
            sprintf('%s.file', Fields::COVER) => 'Обложка должна быть файлом.',
            sprintf('%s.mimes', Fields::COVER) => 'Обложка должна быть в формате JPG или PNG.',
        ];

        $parent = parent::messages();
        $parent['messages'] = array_merge($parent['messages'], $messages);

        return $parent;
    }

}
