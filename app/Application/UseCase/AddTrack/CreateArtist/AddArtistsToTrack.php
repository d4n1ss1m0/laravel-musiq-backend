<?php

namespace App\Application\UseCase\AddTrack\CreateArtist;

use App\Domain\Models\Track;
use App\Domain\Repositories\Artist\ArtistRepositoryInterface;
use App\Service\FileService\FileService;
use getID3;
use Illuminate\Http\UploadedFile;

class AddArtistsToTrack implements AddArtistsToTrackInterface
{
    public function __construct(private readonly FileService               $fileService,
                                private readonly ArtistRepositoryInterface $artistRepository,
    )
    {
    }

    public function handle(Track $track, array $artists)
    {
        $artistsArray = $artists;
        $trackCover = $track->image;
        $fileNameArray = explode('/', $trackCover);
        $fileName = $fileNameArray[count($fileNameArray) -1];

        $uploaded = new UploadedFile(
            storage_path("app/{$trackCover}"),
            $fileName,    // вот здесь указываем расширение
            'image/webp',
            null,
            true
        );

        $existsArtistsArray = [];
        foreach ($artistsArray as $item) {
            if(isset($item->id)) {
                $existsArtistsArray[] = $item->id;;
            } else {
                if($trackCover != null) {
                    $artistCover = $this->fileService->addFile($uploaded, 'image/artist');
                }
                $artist = $this->artistRepository->create($item->name, $artistCover ?? null);
                $item->id = $artist->id;
                $existsArtistsArray[] = $item->id;;
            }
        }
        $track->artists()->syncWithoutDetaching($existsArtistsArray);
    }
}
