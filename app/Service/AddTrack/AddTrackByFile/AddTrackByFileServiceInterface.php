<?php
namespace App\Service\AddTrack\AddTrackByFile;
use App\Http\Requests\AddTrack\AddTrackByFileRequest;

interface AddTrackByFileServiceInterface {

    public function addTrackByFile(AddTrackByFileRequest $request);
}
