<?php
namespace App\Infrastructure\Services\MusicStream;
interface MusicStreamServiceInterface {
    public function stream(string $filePath, ?int $rangeFrom = null, ?int $rangeTo = null):array;
}
