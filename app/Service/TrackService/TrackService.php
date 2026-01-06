<?php

namespace App\Service\TrackService;

use App\Models\Track;
use App\DTO\AddTrack\AddTrackDTO;
use App\Repositories\Artist\ArtistRepositoryInterface;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Service\FileService\FileServiceInterface;
use getID3;
use Illuminate\Http\UploadedFile;

class TrackService implements TrackServiceInterface
{
    public function __construct(
        private readonly FileServiceInterface $fileService,
        private readonly TrackRepositoryInterface $trackRepository,
        private readonly ArtistRepositoryInterface $artistRepository,
    )
    {
    }

    public function addTrackByFile(AddTrackDTO $dto, int $userId)
    {
        $file = $dto->file;
        $getID3 = new getID3();
        $file = $this->fileService->addFile($file, 'audio');
        $file = $this->fileService->convertMusicFile(storage_path('app/audio/'.$file));
        $filePath = storage_path('app/audio/'.$file);
        $fileInfo = $getID3->analyze($filePath);
        $time = (int)round($fileInfo['playtime_seconds']);
        if($dto->cover != null) {
            $cover = $this->fileService->addFile($dto->cover, 'image/track', 'webp');
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

        $this->addArtistsToTrack($track, $artistsArray);

        return $track->id;
    }

    public function addArtistsToTrack(Track $track, array $artists)
    {
        $artistsArray = $artists;
        $fileName = $track->image;


        $existsArtistsArray = [];
        foreach ($artistsArray as $item) {
            if(isset($item->id)) {
                $existsArtistsArray[] = $item->id;;
            } else {
                if($fileName != null) {

                    $uploaded = new UploadedFile(
                        storage_path("app/image/track/{$fileName}"),
                        $fileName,    // вот здесь указываем расширение
                        'image/webp',
                        null,
                        true
                    );
                    $artistCover = $this->fileService->addFile($uploaded, 'image/artist', 'webp');
                }
                $artist = $this->artistRepository->create($item->name, $artistCover ?? null);
                $item->id = $artist->id;
                $existsArtistsArray[] = $item->id;;
            }
        }
        $track->artists()->syncWithoutDetaching($existsArtistsArray);
    }
}
