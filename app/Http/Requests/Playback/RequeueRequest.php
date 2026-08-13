<?php

namespace App\Http\Requests\Playback;

use App\Enum\PlaybackSource;
use App\Enum\RepeatType;
use App\Rules\PlaybackSourceExists;
use App\Rules\TrackPlaybackSourceExists;
use App\Shared\Fields\Fields;
use App\Enums\SearchTypes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RequeueRequest extends FormRequest
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
            Fields::REQUEUE => 'sometimes|bool'
        ];
    }

    public function messages()
    {
        return [
            'messages' => [
                sprintf('%s.bool', Fields::REQUEUE) => 'Флаг перезапуска очереди должен быть булевым',
            ],
        ];
    }

}
