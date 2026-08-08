<?php

namespace App\Http\Controllers\Playback;

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
    public function snapshot()
    {

    }
}
