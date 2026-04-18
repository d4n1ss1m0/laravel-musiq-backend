<?php

namespace App\Service\TrackService;

use App\Enum\MusicService;
use App\Models\Track;
use App\DTO\AddTrack\AddTrackDTO;
use App\Repositories\Artist\ArtistRepositoryInterface;
use App\Repositories\Track\TrackRepositoryInterface;
use App\Service\FileService\FileServiceInterface;
use getID3;
use Illuminate\Http\UploadedFile;
use Symfony\Component\Process\Process;

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


        $this->addArtistsToTrack($track, $artistsArray);

        return $track->uuid;
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

    public function parseFromUrl(string $url, MusicService $musicService) {
        $binary = config('services.musiq_downloader.binary');
        return [
            'audio' => $this->getAudioFile($binary, $musicService, $url),
            'cover' => $this->getCoverFile($binary, $musicService, $url),
            'name' => $this->getTrackName($binary, $musicService, $url),
        ];
    }

    private function getAudioFile(string $binary, MusicService $musicService, string $url) : string|null
    {
        $process = new Process([
            $binary, $musicService->value, 'download',
            '-o', storage_path('app/audio/tmp'), $url
        ]);
        $process->setTimeout(300);
        $process->run();

        $output = $process->getOutput();

// Ищем строку "PATH:  ..." и вытаскиваем путь
        preg_match('/^PATH:\s+(.+)$/m', $output, $matches);

        $filePath = $matches[1] ?? null;

        if ($filePath) {
            $outputFile = basename($output); // d1aca076-8fe5-4840-8720-2b97f83c08b2.mp3
            $relativePath = 'tmp/' . $outputFile;
            return $relativePath;
        } else {
            return null;
        }
    }

    private function getCoverFile(string $binary, MusicService $musicService, string $url) : string|null
    {
        $process = new Process([
            $binary, $musicService->value, 'cover',
            '-o', storage_path('app/image/tmp'), $url
        ]);
        $process->setTimeout(300);
        $process->run();

        $output = $process->getOutput();

// Ищем строку "PATH:  ..." и вытаскиваем путь
        preg_match('/^COVER:\s+(.+)$/m', $output, $matches);

        $filePath = $matches[1] ?? null;

        if ($filePath) {
            $outputFile = basename($output); // d1aca076-8fe5-4840-8720-2b97f83c08b2.mp3
            $relativePath = 'tmp/' . $outputFile;
            return $relativePath;
        } else {
            return null;
        }
    }

    private function getTrackName(string $binary, MusicService $musicService, string $url) : string|null
    {
        $process = new Process([
            $binary, $musicService->value, 'name',
        ]);
        $process->setTimeout(300);
        $process->run();

        $output = $process->getOutput();

// Ищем строку "PATH:  ..." и вытаскиваем путь
        preg_match('/^NAME:\s+(.+)$/m', $output, $matches);

        $filePath = $matches[1] ?? null;

        return $filePath;
    }
}
