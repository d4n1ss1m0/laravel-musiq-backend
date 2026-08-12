<?php

namespace App\Http\Controllers\Playback;

use App\DTO\Playback\SnapshotDTO;
use App\Enum\PlaybackSource;
use App\Enum\PlaybackState;
use App\Enum\RepeatType;
use App\Http\Requests\Playback\ShuffleRequest;
use App\Http\Requests\Playback\SnapshotRequest;
use App\Http\Resources\Playback\PlaybackSessionResource;
use App\Service\PlaybackService\PlaybackServiceInterface;
use App\Shared\Fields\Fields;
use App\Http\Controllers\Controller;
use App\Http\Requests\Utility\PaginateRequest;
use App\Http\Resources\Tracks\TrackResource;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class PlaybackController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly PlaybackServiceInterface $playbackService)
    {
    }

    public function snapshot(SnapshotRequest $request)
    {
        try {
            $userId = $request->attributes->get(Fields::USER_ID);
            $dto = new SnapshotDTO(
                PlaybackSource::tryFrom($request->input(Fields::SOURCE)),
                $request->input(Fields::SOURCE_ID),
                $request->input(Fields::ID),
                RepeatType::tryFrom($request->input(Fields::REPEAT)),
                (bool)$request->input(Fields::SHUFFLE)
            );
            $this->playbackService->snapshot($dto, $userId);
            return $this->success('success');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function shuffle(ShuffleRequest $request)
    {
        try {
            $userId = $request->attributes->get(Fields::USER_ID);
            $session = $this->playbackService->shuffle((bool)$request->input(Fields::SHUFFLE), $userId);

            return $this->success(new PlaybackSessionResource($session));
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function next(Request $request)
    {
        try {
            $userId = $request->attributes->get(Fields::USER_ID);

            $session = $this->playbackService->next($userId, $request->input('requeue'));

            return $this->success(new PlaybackSessionResource($session));
        } catch (\Throwable $t) {
            return $this->error($t->getMessage());
        }
    }

    public function prev(Request $request)
    {
        try {
            $userId = $request->attributes->get(Fields::USER_ID);

            $session = $this->playbackService->previous($userId);

            return $this->success(new PlaybackSessionResource($session));
        } catch (\Throwable $t) {
            return $this->error($t->getMessage());
        }
    }

    public function repeat(Request $request)
    {
        try {
            $userId = $request->attributes->get(Fields::USER_ID);
            $repeatMode = RepeatType::tryFrom($request->input('repeatMode'));

            $session = $this->playbackService->repeat($repeatMode, $userId);

            return $this->success(new PlaybackSessionResource($session));
        } catch (\Throwable $t) {
            return $this->error($t->getMessage());
        }
    }

    public function play(Request $request)
    {
        try {
            $userId = $request->attributes->get(Fields::USER_ID);

            $session = $this->playbackService->changeState(PlaybackState::PLAYING, $userId);

            return $this->success(new PlaybackSessionResource($session));
        } catch (\Throwable $t) {
            return $this->error($t->getMessage());
        }
    }

    public function pause(Request $request)
    {
        try {
            $userId = $request->attributes->get(Fields::USER_ID);

            $session = $this->playbackService->changeState(PlaybackState::PAUSED, $userId);

            return $this->success(new PlaybackSessionResource($session));
        } catch (\Throwable $t) {
            return $this->error($t->getMessage());
        }
    }
}
