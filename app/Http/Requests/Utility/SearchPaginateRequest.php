<?php

namespace App\Http\Requests\Utility;

use App\Shared\Fields\Fields;
use App\Enums\SearchTypes;
use Illuminate\Foundation\Http\FormRequest;

class SearchPaginateRequest extends PaginateRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            Fields::QUERY => 'sometimes|string|max:255',
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            sprintf('%s.string', Fields::QUERY) => 'Запрос должен быть строкой',
            sprintf('%s.max', Fields::QUERY) => 'Запрос должен быть не более 255 строк',
        ]);
    }

}
