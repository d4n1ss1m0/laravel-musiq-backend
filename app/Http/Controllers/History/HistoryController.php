<?php

namespace App\Http\Controllers\History;

use App\DTO\History\HistoryItemDTO;
use App\Enum\HistorySource;
use App\Http\Controllers\Controller;
use App\Http\Requests\History\AddToHistoryRequest;
use App\Service\AuthService\AuthServiceInterface;
use App\Service\HistoryService\HistoryServiceInterface;
use App\Shared\Fields\Fields;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly HistoryServiceInterface $historyService)
    {
    }

    public function store(AddToHistoryRequest $request)
    {
        try {
            $userId = $request->attributes->get('userId');
            $sourceString = $request->input(Fields::SOURCE);
            $source = HistorySource::tryFrom($sourceString);
            $trackId = $request->input(Fields::ID);
            $sourceId = $request->input(Fields::SOURCE_ID);

            $this->historyService->store($userId, $trackId, $sourceId, $source);

            return $this->success('success');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
