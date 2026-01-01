<?php

namespace App\Http\Controllers\Player;

use App\Events\TrackPlayed;
use App\Http\Controllers\Controller;
use App\Service\MusicStream\MusicStreamServiceInterface;
use App\Service\Player\PlayerService;
use App\Shared\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MusicStreamController extends Controller
{
    use HttpResponse;
    public function stream(string $id, Request $request, PlayerService $useCase)
    {
        $userId = $request->attributes->get('userId');
        if(request()->headers->has('Range')) {
            $range = request()->headers->get('Range');
            $range = preg_replace('/bytes=/', '', $range);
            $range = explode('-', $range);

            $start = (int)$range[0];
            $end = isset($range[1]) && $range[1] !== '' ? (int)$range[1] : null;
        } else {
            $start = null;
            $end = null;
        };
        Log::info("Range {$start}-{$end}");

        [$filePath, $fileSize, $callback, $start, $end] = $useCase->streamData($id, $start, $end);
//        dd($end);
//        dd([$filePath, $fileSize, $callback, $start, $end]);
        if ($userId) {
            event(new TrackPlayed($userId, $id));
        }
        return $this->musicStream($filePath, $callback, $fileSize, $start, $end);
    }
}
