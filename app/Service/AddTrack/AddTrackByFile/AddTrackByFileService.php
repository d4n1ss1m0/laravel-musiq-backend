<?php

namespace App\Service\AddTrack\AddTrackByFile;

use App\Http\Requests\AddTrack\AddTrackByFileRequest;
use App\Domain\Entities\Track;
use App\Service\AddTrack\AddTrackService;
use App\Service\Auth\AccountService;
use App\Service\FileService\FileService;
use App\Shared\Traits\HttpResponse;
use getID3;

class AddTrackByFileService extends AddTrackService implements AddTrackByFileServiceInterface
{
    use HttpResponse;

    public function __construct(AccountService $accountService, FileService $fileService)
    {
        parent::__construct($accountService, $fileService);
    }

    public function addTrackByFile(AddTrackByFileRequest $request)
    {
        //dd($request->input('artists'));
        $file = $request->file('file');
        $getID3 = new getID3();
        $file = $this->fileService->addFile($file, 'audio');
        $file = $this->fileService->convertMusicFile(storage_path('app/'.$file));
        $filePath = storage_path('app/audio/'.$file);
        $fileInfo = $getID3->analyze($filePath);
        $time = (int)round($fileInfo['playtime_seconds']);
        if($request->file('cover') != null) {
            $cover = $this->fileService->addFile($request->file('cover'), 'image/track');
        }
        $track = $this->createTrack($request->input('name'), $time, $file, $cover ?? null);
        $artistsArray = $request->input('artists');
        $existsArtistsArray = [];
        foreach ($artistsArray as $item) {
            if(isset($item['id'])) {
                $existsArtistsArray[] = $item;
            } else {
                if($request->file('cover') != null) {
                   $artistCover = $this->fileService->addFile($request->file('cover'), 'image/artist');
                }
                $artist = $this->createArtist($item, $artistCover ?? null);
                $existsArtistsArray[] = [
                    'id' => $artist->id,
                    'name' => $artist->name
                ];
            }
        }
        $this->addArtistsToTrack($existsArtistsArray, $track->id);
        return [
            'trackId' => $track->id,
        ];
    }


}
