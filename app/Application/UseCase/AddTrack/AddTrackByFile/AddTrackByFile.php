<?php

namespace App\Application\UseCase\AddTrack\AddTrackByFile;

use App\Application\DTO\AddTrack\AddTrackDTO;
use App\Application\UseCase\AddTrack\CreateArtist\AddArtistsToTrack;
use App\Application\UseCase\AddTrack\CreateArtist\AddArtistsToTrackInterface;
use App\Domain\Repositories\Artist\ArtistRepositoryInterface;
use App\Domain\Repositories\Track\TrackRepositoryInterface;
use App\Service\FileService\FileService;
use getID3;

class AddTrackByFile implements AddTrackByFileInterface
{
    public function __construct(private readonly FileService               $fileService,
                                private readonly TrackRepositoryInterface  $trackRepository,
                                private readonly AddArtistsToTrackInterface $addArtistsToTrack,
    )
    {
    }

    public function handle(AddTrackDTO $dto, int $userId)
    {
        $file = $dto->file;
        $getID3 = new getID3();
        $file = $this->fileService->addFile($file, 'audio');
        $file = $this->fileService->convertMusicFile(storage_path('app/'.$file));
        $filePath = storage_path('app/'.$file);
        $fileInfo = $getID3->analyze($filePath);
        $time = (int)round($fileInfo['playtime_seconds']);
        if($dto->cover != null) {
            $cover = $this->fileService->addFile($dto->cover, 'image/track');
        }
        $track = $this->trackRepository->create($dto->name, $time, $file, $cover ?? null, $userId);
        $artistsArray = $dto->artists;
//        $existsArtistsArray = [];
//        foreach ($artistsArray as $item) {
//            if(isset($item->id)) {
//                $existsArtistsArray[] = $item->id;
//            } else {
//                if($dto->cover != null) {
//                    $artistCover = $this->fileService->addFile($dto->cover, 'image/artist');
//                }
//                $artist = $this->artistRepository->create($item->name, $artistCover ?? null);
//                $item->id = $artist->id;
//                $existsArtistsArray[] = $item->id;
//            }
//        }
//        $track->artists()->syncWithoutDetaching($existsArtistsArray);

        $this->addArtistsToTrack->handle($track, $artistsArray);

        return $track->id;
    }
}
