<?php

namespace App\Http\Requests\History;

use App\Enum\HistorySource;
use App\Shared\Fields\Fields;
use Illuminate\Foundation\Http\FormRequest;

class AddToHistoryRequest extends FormRequest
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
            Fields::ID => ['required', 'string'],

            Fields::SOURCE => [
                'nullable',
                'string',
                'required_with:' . Fields::SOURCE_ID,
                'in:' . implode(',', array_column(HistorySource::cases(), 'value')),
            ],

            Fields::SOURCE_ID => [
                'nullable',
                'string',
                'required_with:' . Fields::SOURCE,
            ],
        ];
    }

    public function messages()
    {
        return [
            sprintf('%s.string', Fields::SOURCE) => 'Источник должен являться строкой',
            sprintf('%s.required_with', Fields::SOURCE) => 'Источник необходимо указать вместе с id источника',
            sprintf('%s.in', Fields::SOURCE) => 'Некорректный источник',

            sprintf('%s.string', Fields::SOURCE_ID) => 'Айди источника должен являться строкой',
            sprintf('%s.required_with', Fields::SOURCE_ID) => 'Id источника необходимо указать вместе с источником',

            sprintf('%s.required', Fields::ID) => 'Необходимо указать id трека',
            sprintf('%s.string', Fields::ID) => 'Id трека должен являться строкой',
        ];
    }

}
