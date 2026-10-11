<?php

namespace App\Http\Controllers\History;

use App\DTO\History\ListenHistoryDTO;
use App\Enum\HistorySource;
use App\Http\Controllers\Controller;
use App\Http\Requests\History\AddListenToHistoryRequest;
use App\Http\Requests\History\AddToHistoryRequest;
use App\Http\Requests\Utility\PaginateRequest;
use App\Service\HistoryService\HistoryServiceInterface;
use App\Shared\Fields\Fields;
use App\Shared\Traits\HttpResponse;
use Carbon\CarbonImmutable;

class HistoryController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly HistoryServiceInterface $historyService) {}

    /**
     * @deprecated Use POST /api/history/listen.
     */
    public function store(AddToHistoryRequest $request)
    {
        $userId = $request->attributes->get('userId');
        $sourceString = $request->input(Fields::SOURCE);
        $source = $sourceString !== null ? HistorySource::tryFrom($sourceString) : null;
        $trackId = $request->input(Fields::ID);
        $sourceId = $request->input(Fields::SOURCE_ID);

        $this->historyService->store($userId, $trackId, $sourceId, $source);

        return $this->success('success');
    }

    public function listen(AddListenToHistoryRequest $request)
    {
        $dto = new ListenHistoryDTO(
            userId: (int) $request->attributes->get('userId'),
            attemptId: $request->string('attemptId')->toString(),
            trackId: $request->string(Fields::ID)->toString(),
            source: $request->filled(Fields::SOURCE)
                ? HistorySource::from($request->input(Fields::SOURCE))
                : null,
            sourceId: $request->input(Fields::SOURCE_ID),
            startedAt: CarbonImmutable::parse($request->input('startedAt')),
            listenedSeconds: $request->integer('listenedSeconds'),
            finished: $request->boolean('finished'),
        );

        return $this->success($this->historyService->listen($dto));
    }

    public function get(PaginateRequest $request)
    {
        try {
            $userId = $request->attributes->get('userId');
            $limit = (int) $request->query(Fields::PER_PAGE, 10);
            $page = (int) $request->query(Fields::PAGE, 1);

            return $this->success($this->historyService->get($userId, $limit, $page));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
