<?php

namespace App\Http\Middleware;

use App\Models\Auth\User;
use App\Models\Playlist;
use App\Service\JwtService\JwtServiceInterface;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsPlaylistExistsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $playlistId = $request->route('uuid');
        if (!$playlistId) {
            return response()->json(['error' => 'Uuid not found'], 404);
        }

        $playlistExists = Playlist::query()->where('uuid', $playlistId)->exists();

        if (!$playlistExists) {
            return response()->json(['error' => 'Playlist not found'], 404);
        }

        return $next($request);
    }
}
