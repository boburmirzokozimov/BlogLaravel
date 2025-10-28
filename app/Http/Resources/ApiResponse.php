<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiResponse
{
    /**
     * Return a success response with bilingual message.
     */
    public static function success(
        string $messageKey, JsonResource|AnonymousResourceCollection|null $data, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => [
                'en' => trans("messages.{$messageKey}", [], 'en'),
                'ru' => trans("messages.{$messageKey}", [], 'ru'),
            ],
            'data' => $data,
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Return an error response with bilingual message.
     */
    public static function error(string $messageKey, int $statusCode = 400, array $data = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => [
                'en' => trans("messages.{$messageKey}", [], 'en'),
                'ru' => trans("messages.{$messageKey}", [], 'ru'),
            ],
            'data' => $data,
        ];

        return response()->json($response, $statusCode);
    }

}
