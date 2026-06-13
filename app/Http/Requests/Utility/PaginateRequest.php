<?php

namespace App\Http\Requests\Utility;

use App\Shared\Fields\Fields;
use App\Enums\SearchTypes;
use Illuminate\Foundation\Http\FormRequest;

class PaginateRequest extends FormRequest
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
            Fields::PAGE => 'sometimes|integer|min:1',
            Fields::PER_PAGE => sprintf(
                "sometimes|integer|max:%d|min:%d",
                config('app.per_page_max_default'),
                config('app.per_page_min_default')
            ),
        ];
    }

    public function messages(): array
    {
        return [
            sprintf('%s.integer'. Fields::PAGE) => 'Номер страницы должен быть целым числом.',
            sprintf('%s.min'. Fields::PAGE) => 'Номер страницы должен быть не меньше 1.',

            sprintf('%s.integer'. Fields::PER_PAGE) => 'Количество элементов на странице должно быть целым числом.',
            sprintf('%s.min'. Fields::PER_PAGE) => sprintf(
                'Количество элементов на странице должно быть не меньше %d.',
                config('app.per_page_min_default')
            ),
            sprintf('%s.max'. Fields::PER_PAGE) => sprintf(
                'Количество элементов на странице должно быть не больше %d.',
                config('app.per_page_max_default')
            ),
        ];
    }

}
