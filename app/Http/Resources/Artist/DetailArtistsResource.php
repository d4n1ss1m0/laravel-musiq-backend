<?php

namespace App\Http\Resources\Artist;

use App\Http\Resources\TrackResource;
use App\Service\Auth\AccountService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DetailArtistsResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $accountService = app()->make(AccountService::class);
        return[
            'id' => $this->id,
            'name' => $this->name,
            'cover' => $this->image ?'/image/'.$this->image : '',
            'tracks' => TrackResource::collection($this->tracks),
            'favouriteCount' => $this->favouriteCount(),
            'isFavourite' => $accountService->getAccountId() == null ? $this->users()->where('users.id', $accountService->getAccountId())->exists() : false
        ];
    }
}
