<?php
namespace App\Service\AddTrack;
use App\Http\Requests\AddTrack\AddTrackByFileRequest;

interface AddTrackServiceInterface {

    public function createTrack($name, $time, $song, $image);
}
