<?php

namespace App\Http\Middleware;

use App\Models\Auth\User;
use App\Models\Playlist;
use App\Service\JwtService\JwtServiceInterface;
use App\Shared\Enums\PlaylistTypes;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanManagePlaylistMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $playlistId = $request->route('uuid');
        $playlist = Playlist::query()->select('id', 'user_id', 'type')->where('uuid', $playlistId)->first();

        $isMyPlaylist = $playlist->user_id == $request->attributes->get('userId');

        if ($isMyPlaylist) {
            return $next($request);
        }

        return response()->json(['error' => 'Playlist not found'], 404);
    }
}
