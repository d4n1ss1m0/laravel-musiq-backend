<?php

namespace App\Http\Controllers\Playback;

use App\DTO\Playback\SnapshotDTO;
use App\Enum\PlaybackSource;
use App\Enum\RepeatType;
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

    public function snapshot(Request $request)
    {
        try {
            $userId = $request->attributes->get('userId');
            $dto = new SnapshotDTO(
                PlaybackSource::tryFrom($request->input('source')),
                $request->input('sourceId'),
                $request->input('trackId'),
                RepeatType::tryFrom($request->input('repeatType')),
                (bool)$request->input('shuffle')
            );
            $this->playbackService->snapshot($dto, $userId);
            return $this->success('success');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
