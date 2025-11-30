<?php

namespace App\Service\Player\MusicStream;

use App\Http\Requests\Auth\LoginRequest;
use App\Domain\Entities\Track;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MusicStreamService implements MusicStreamServiceInterface
{
    public function streamV1($id)
    {
        // Получите путь к аудиофайлу по ID
        $filePath = storage_path("app/audio/{$id}.mp3"); // Измените путь на свой

        if (!file_exists($filePath)) {
            abort(404);
        }

        $response = new StreamedResponse(function () use ($filePath) {
            $handle = fopen($filePath, 'rb');
            while (!feof($handle)) {
                echo fread($handle, 1024);
                flush(); // Освобождает память
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'audio/mpeg');
        $response->headers->set('Content-Disposition', 'inline; filename="' . basename($filePath) . '"');
        return $response;
    }

    public function stream($id)
    {
        $song = Track::where('id', $id)->first('song')->song;

        // Получаем путь к аудиофайлу по ID
        $filePath = storage_path("app/audio/{$song}");

        // Проверяем, существует ли файл
        if (!file_exists($filePath)) {
            abort(404);
        }

        // Получаем размер файла
        $fileSize = filesize($filePath);
        $response = new StreamedResponse();

        // Устанавливаем заголовки
        $response->headers->set('Content-Type', 'audio/webm');
        $response->headers->set('Content-Disposition', 'inline; filename="' . basename($filePath) . '"');
        $response->headers->set('Accept-Ranges', 'bytes');

        // Проверяем наличие заголовка Range
        if (request()->headers->has('Range')) {
            $range = request()->headers->get('Range');
            $range = preg_replace('/bytes=/', '', $range);
            $range = explode('-', $range);

            $start = (int)$range[0];
            $end = isset($range[1]) && $range[1] !== '' ? (int)$range[1] : $fileSize - 1;

            // Убедитесь, что диапазон корректен
            if ($start > $end || $start > $fileSize - 1 || $end >= $fileSize) {
                return response()->json(['error' => 'Requested range not satisfiable'], 416);
            }

            // Устанавливаем заголовки для диапазона
            $response->headers->set('Content-Range', 'bytes ' . $start . '-' . $end . '/' . $fileSize);
            $response->headers->set('Content-Length', $end - $start + 1);
            $response->setStatusCode(206); // Устанавливаем статус 206 (Partial Content)
        } else {
            // Если нет диапазона, устанавливаем полный размер файла
            $start = 0;
            $end = $fileSize - 1;
            $response->headers->set('Content-Length', $fileSize);
        }

        // Обрабатываем поток
        $response->setCallback(function () use ($filePath, $start, $end) {
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
        });

        return $response;
    }


}
