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

class SnapshotRequest extends FormRequest
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
            Fields::SOURCE => ['required', 'string', Rule::enum(PlaybackSource::class)],
            Fields::SOURCE_ID => ['required', 'string'],
            Fields::ID => ['required', 'string'],
            Fields::REPEAT => ['required', 'string', Rule::enum(RepeatType::class)],
            Fields::SHUFFLE => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $source = PlaybackSource::from($this->input(Fields::SOURCE));
            $sourceId = $this->input(Fields::SOURCE_ID);
            $trackId = $this->input(Fields::ID);

            $sourceRule = new PlaybackSourceExists(true, $source);
            $sourceRule->validate(Fields::SOURCE_ID, $sourceId, function ($message) use ($validator) {
                $validator->errors()->add(Fields::SOURCE_ID, $message);
            });

            $trackRule = new TrackPlaybackSourceExists(true, $source, $sourceId);
            $trackRule->validate(Fields::ID, $trackId, function ($message) use ($validator) {
                $validator->errors()->add(Fields::ID, $message);
            });
        });
    }

    public function messages()
    {
        return [
            'messages' => [
                sprintf('%s.required', Fields::NAME) => 'Название трека обязательно для заполнения.',
                sprintf('%s.string', Fields::NAME)  => 'Название трека должно быть строкой.',

                sprintf('%s.required', Fields::ARTISTS)  => 'Укажите хотя бы одного артиста.',
                sprintf('%s.array', Fields::ARTISTS)  => 'Список артистов должен быть массивом.',
                sprintf('%s.*.%s.string', Fields::ARTISTS, Fields::ID) => 'Идентификатор артиста должен быть строкой.',
                sprintf('%s.*.%s.required', Fields::ARTISTS, Fields::NAME)  => 'Имя каждого артиста обязательно.',
                sprintf('%s.*.%s.string', Fields::ARTISTS, Fields::NAME)  => 'Имя артиста должно быть строкой.',
            ],
        ];
    }

}
