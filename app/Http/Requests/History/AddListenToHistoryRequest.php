<?php

namespace App\Http\Requests\History;

use App\Enum\HistorySource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddListenToHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attemptId' => ['required', 'uuid'],
            'id' => ['required', 'uuid'],
            'source' => [
                'nullable',
                'required_with:sourceId',
                Rule::enum(HistorySource::class),
            ],
            'sourceId' => ['nullable', 'required_with:source', 'uuid'],
            'startedAt' => ['required', 'date', 'before_or_equal:now'],
            'listenedSeconds' => ['required', 'integer', 'min:0'],
            'finished' => ['sometimes', 'boolean'],
        ];
    }
}
