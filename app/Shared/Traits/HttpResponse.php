<?php

namespace App\Shared\Traits;

use Symfony\Component\HttpFoundation\StreamedResponse;

trait HttpResponse
{
    public function success($value, $status = 'success', $code = 200) {
        return json_encode([
            'data' => $value,
            'status' => $status,
            'code' => $code
        ]);
    }

    public function error($value, $status = 'error', $code = 500) {
        return response()->json(['data' => $value,
            'status' => $status,
            'code' => $code], $code);
    }

    public function musicStream(string $filePath, callable $callback, int $fileSize, ?int $start, ?int $end):StreamedResponse
    {
        $response = new StreamedResponse();

        // Устанавливаем заголовки
        $response->headers->set('Content-Type', 'audio/webm');
        $response->headers->set('Content-Disposition', 'inline; filename="' . basename($filePath) . '"');
        $response->headers->set('Accept-Ranges', 'bytes');

        if(isset($start) && isset($end)) {
            $response->headers->set('Content-Range', 'bytes ' . $start . '-' . $end . '/' . $fileSize);
            $response->headers->set('Content-Length', $end - $start + 1);
            $response->setStatusCode(206); // Устанавливаем статус 206 (Partial Content)
        } else {
            $response->headers->set('Content-Length', $fileSize);
        }

        $response->setCallback($callback);

        return $response;
    }
}
