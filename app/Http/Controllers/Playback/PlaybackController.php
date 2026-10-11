<?php

namespace App\Http\Controllers\Playback;

use App\DTO\Playback\QueueResponseDTO;
use App\DTO\Playback\SnapshotDTO;
use App\Enum\PlaybackManualType;
use App\Enum\PlaybackSource;
use App\Enum\PlaybackState;
use App\Enum\RepeatType;
use App\Http\Requests\Playback\AddToQueueRequest;
use App\Http\Requests\Playback\MoveTrackRequest;
use App\Http\Requests\Playback\PauseRequest;
use App\Http\Requests\Playback\PlayRequest;
use App\Http\Requests\Playback\RepeatRequest;
use App\Http\Requests\Playback\RequeueRequest;
use App\Http\Requests\Playback\ShuffleRequest;
use App\Http\Requests\Playback\SnapshotRequest;
use App\Http\Resources\Playback\PlaybackSessionResource;
use App\Service\PlaybackService\PlaybackServiceInterface;
use App\Shared\Fields\Fields;
use App\Http\Controllers\Controller;
use App\Http\Requests\Utility\PaginateRequest;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class PlaybackController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly PlaybackServiceInterface $playbackService)
    {
    }

    public function queue(PaginateRequest $request)
    {
        $userId = $request->attributes->get(Fields::USER_ID);
        $perPage = $request->integer(Fields::PER_PAGE, config('app.per_page_default'));
        [$session, $queue] = $this->playbackService->getQueue($userId, $perPage);
        $keyValueArray = new QueueResponseDTO($queue, $session);
        return $this->success($this->paginator($keyValueArray->getResponse(), $queue->total(), $queue->perPage(), $queue->currentPage()));
    }

    public function snapshot(SnapshotRequest $request)
    {
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
    }

    public function shuffle(ShuffleRequest $request)
    {
        $userId = $request->attributes->get(Fields::USER_ID);
        $session = $this->playbackService->shuffle((bool)$request->input(Fields::SHUFFLE), $userId);

        return $this->success(new PlaybackSessionResource($session));
    }

    public function repeat(RepeatRequest $request)
    {
        $userId = $request->attributes->get(Fields::USER_ID);
        $repeatMode = RepeatType::tryFrom($request->input(Fields::REPEAT));

        $session = $this->playbackService->repeat($repeatMode, $userId);

        return $this->success(new PlaybackSessionResource($session));
    }

    public function next(Request $request)
    {
        $userId = $request->attributes->get(Fields::USER_ID);

        $session = $this->playbackService->next($userId, $request->boolean(Fields::REQUEUE, false));

        return $this->success(new PlaybackSessionResource($session));
    }

    public function prev(Request $request)
    {
        $userId = $request->attributes->get(Fields::USER_ID);

        $session = $this->playbackService->previous($userId);

        return $this->success(new PlaybackSessionResource($session));
    }

    public function play(PlayRequest $request)
    {
        $userId = $request->attributes->get(Fields::USER_ID);

        $session = $this->playbackService->play($userId, $request->input(Fields::PLACEMENT, null));

        return $this->success(new PlaybackSessionResource($session));
    }

    public function pause(PauseRequest $request)
    {
        $userId = $request->attributes->get(Fields::USER_ID);

        $session = $this->playbackService->pause($userId, $request->integer(Fields::SECONDS));

        return $this->success(new PlaybackSessionResource($session));
    }

    public function add(AddToQueueRequest $request)
    {
        try {
            $userId = $request->attributes->get(Fields::USER_ID);
            $trackId = $request->input(Fields::ID);
            $type = $request->input(Fields::TYPE);
            $type = PlaybackManualType::tryFrom($type);

            $this->playbackService->addToQueue($userId, $type, $trackId);
            return $this->success('success');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    public function move(MoveTrackRequest $request)
    {
        $userId = $request->attributes->get(Fields::USER_ID);
        $position = $request->integer(Fields::POSITION);
        $currentPosition = $request->integer(Fields::CURRENT_POSITION);

        $this->playbackService->moveTrack($userId, $currentPosition, $position);
        return $this->success('success');
    }
}
