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

class AddTrackByFileRequest extends AddTrackBaseRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'file' => 'required|file|mimes:mp3,flac,wav',
            'cover' => 'required|file|mimes:jpg,png,webp'
        ]);
    }

    public function messages()
    {
        $messages = [
            'file.required' => 'Пожалуйста, загрузите аудиофайл.',
            'file.file' => 'Файл должен быть корректным.',
            'file.mimes' => 'Аудиофайл должен быть в формате MP3 или FLAC.',

            'cover.required' => 'Пожалуйста, загрузите обложку.',
            'cover.file' => 'Обложка должна быть файлом.',
            'cover.mimes' => 'Обложка должна быть в формате JPG или PNG.',
        ];

        $parent = parent::messages();
        $parent['messages'] = array_merge($parent['messages'], $messages);

        return $parent;
    }

}
