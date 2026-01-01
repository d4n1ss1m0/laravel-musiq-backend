<?php

namespace App\Service\MusicStream;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\Track;
use App\Models\User;
use App\Service\Auth\AuthServiceInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MusicStreamService implements MusicStreamServiceInterface
{
    public function stream(string $filePath, ?int $rangeFrom = null, ?int $rangeTo = null):array
    {
        // Проверяем, существует ли файл
        if (!file_exists($filePath)) {
            abort(404);
        }

        // Получаем размер файла
        $fileSize = filesize($filePath);


        // Проверяем наличие заголовка Range
        if (isset($rangeFrom)) {
            $start = $rangeFrom;
            $end = isset($rangeTo) && $rangeTo !== '' ? $rangeTo : $fileSize - 1;

            // Убедитесь, что диапазон корректен
            if ($start > $end || $start > $fileSize - 1) {
                Log::info("MusicStream: {$start}-{$end}/{$fileSize}");
                throw new \Exception('Requested range not satisfiable', 416);
            }

            if ($end === null || $end >= $fileSize) {
                $end = $fileSize - 1;
            }

        } else {
            // Если нет диапазона, устанавливаем полный размер файла
            $start = 0;
            $end = $fileSize - 1;
        }

        // Обрабатываем поток
        $callback = function () use ($filePath, $start, $end) {
            $handle = fopen($filePath, 'rb');
            fseek($handle, $start); // Перемещаем указатель на начало диапазона

            $bytesToRead = $end - $start + 1;
            while ($bytesToRead > 0 && !feof($handle)) {
                $buffer = fread($handle, min(1024, $bytesToRead));
                echo $buffer;
                flush(); // Освобождает память
                $bytesToRead -= strlen($buffer);
            }
            fclose($handle);
        };

        return [$callback, $fileSize, $start, $end];
    }


}
