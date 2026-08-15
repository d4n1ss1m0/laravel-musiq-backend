<?php

namespace App\Http\Controllers\Mediateka;

use App\Enum\MediatekaItemType;
use App\Enum\OrderBy;
use App\Http\Requests\Mediateka\MediaUUIDRequest;
use App\Http\Resources\Mediateka\MediatekaItemResource;
use App\Service\MediatekaService\MediatekaServiceInterface;
use App\Http\Controllers\Controller;
use App\Shared\Fields\Fields;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;

class MediatekaController extends Controller
{
    use HttpResponse;

    public function __construct(
        private readonly MediatekaServiceInterface $mediatekaService,
    )
    {

    }

    public function getMediateka(Request $request)
    {
        try {
            $userId = $request->attributes->get(Fields::USER_ID);
            $orderBy = OrderBy::tryFrom($request->input(Fields::ORDER)) ?? OrderBy::CREATED_AT;

            $mediateka = $this->mediatekaService->getMediateka($userId, $orderBy, $request->get('query') ?? '');
            return MediatekaItemResource::collection($mediateka);
        } catch ( \Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function addMedia(MediaUUIDRequest $request, string $type)
    {
        try{
            $userId = $request->attributes->get(Fields::USER_ID);
            $mediaId = $request->input(Fields::ID);

            $mediaType = MediatekaItemType::tryFrom($type);

            $this->mediatekaService->addMedia($mediaType, $mediaId, $userId);

            return $this->success('success');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function removeMedia(MediaUUIDRequest $request, string $type)
    {
        try{
            $userId = $request->attributes->get(Fields::USER_ID);
            $mediaId = $request->input(Fields::ID);

            $mediaType = MediatekaItemType::tryFrom($type);

            $this->mediatekaService->removeMedia($mediaType, $mediaId, $userId);

            return $this->success('success');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function pinItem(MediaUUIDRequest $request, string $type)
    {
        try {
            $userId = $request->attributes->get(Fields::USER_ID);
            $mediaId = $request->input(Fields::ID);
            $type = MediatekaItemType::tryFrom($type);
            $this->mediatekaService->pinMedia($type, $mediaId, $userId);

            return $this->success('success');
        } catch ( \Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function unpinItem(MediaUUIDRequest $request, string $type)
    {
        try {
            $userId = $request->attributes->get(Fields::USER_ID);
            $mediaId = $request->input(Fields::ID);
            $type = MediatekaItemType::tryFrom($type);
            $this->mediatekaService->unpinMedia($type, $mediaId, $userId);

            return $this->success('success');
        } catch ( \Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
