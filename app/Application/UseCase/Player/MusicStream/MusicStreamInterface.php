<?php

namespace App\Application\UseCase\Player\MusicStream;

interface MusicStreamInterface
{
    public function handle(int $id, ?int $rangeFrom, ?int $rangeTo):array;
}
