<?php

namespace App\Http\Requests\Playlist;

use App\Http\Requests\Utility\SearchPaginateRequest;
use App\Shared\Enums\PlaylistTypes;
use App\Shared\Fields\Fields;
use Illuminate\Foundation\Http\FormRequest;

class PlaylistTracksRequest extends SearchPaginateRequest
{

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            Fields::ORDER => 'sometimes|string|max:255',
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            sprintf('%s.string', Fields::ORDER) => 'Запрос должен быть строкой',
            sprintf('%s.max', Fields::ORDER) => 'Запрос должен быть не более 255 строк',
        ]);
    }

}
