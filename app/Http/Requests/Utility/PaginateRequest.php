<?php

namespace App\Http\Requests\Utility;

use App\Enums\SearchTypes;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
            'page' => 'sometimes|integer|min:1',
            'perPage' => sprintf(
                "sometimes|integer|max:%d|min:%d",
                config('app.per_page_max_default'),
                config('app.per_page_min_default')
            ),
        ];
    }

    public function messages(): array
    {
        return [
            'page.integer' => 'Номер страницы должен быть целым числом.',
            'page.min' => 'Номер страницы должен быть не меньше 1.',

            'perPage.integer' => 'Количество элементов на странице должно быть целым числом.',
            'perPage.min' => sprintf(
                'Количество элементов на странице должно быть не меньше %d.',
                config('app.per_page_min_default')
            ),
            'perPage.max' => sprintf(
                'Количество элементов на странице должно быть не больше %d.',
                config('app.per_page_max_default')
            ),
        ];
    }

}
