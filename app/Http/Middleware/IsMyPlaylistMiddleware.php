<?php

namespace App\Http\Middleware;

use App\Enum\MediatekaItemType;
use App\Exceptions\NotFoundException;
use App\Models\Playlist;
use App\Shared\Enums\PlaylistTypes;
use App\Shared\Fields\Fields;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsMyPlaylistMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, bool|string $isOwner = true): Response
    {
        if ($request->route('type') !== MediatekaItemType::PLAYLIST->value) {
            return $next($request);
        }

        $shouldBeOwner = filter_var($isOwner, FILTER_VALIDATE_BOOLEAN);
        $playlistId = $request->route(Fields::UUID) ?? $request->input(Fields::ID);
        $userId = $request->attributes->get(Fields::USER_ID);

        $playlist = Playlist::query()->where('uuid', $playlistId)->first();
        if (!$playlist) {
            throw new NotFoundException('Playlist not found');
        }

        $playlistOwner = $playlist->user_id;

        if (!$playlistOwner) {
            throw new NotFoundException('Playlist not found');
        }

        $isOwnerValue = $playlistOwner == $userId;

        if ($isOwnerValue !== $shouldBeOwner) {
            throw new NotFoundException('Playlist not found');
        }

        if (!$isOwnerValue) {
            $isPublic = $playlist->type === PlaylistTypes::PUBLIC->getId();
            if (!$isPublic) {
                throw new NotFoundException('Playlist not found');
            }
        }

        return $next($request);
    }
}
