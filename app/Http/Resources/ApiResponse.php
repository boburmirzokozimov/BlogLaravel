<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class ApiResponse
{
    /**
     * Create a standardized success response
     */
    public static function success(
        mixed $data = null,
        string $messageKey = 'messages.success',
        array $messageParams = [],
        int $statusCode = 200
    ): JsonResponse {
        $response = [
            'code' => 'SUCCESS',
            'message' => [
                'en' => __($messageKey, $messageParams, 'en'),
                'ru' => __($messageKey, $messageParams, 'ru'),
            ],
        ];

        if ($data !== null) {
            // If data is a JsonResource, convert it to array
            if ($data instanceof JsonResource) {
                $data = $data->toArray(request());
            }
            
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Create a standardized error response
     */
    public static function error(
        string $code,
        string $messageKey,
        array $messageParams = [],
        mixed $errorDetails = null,
        int $statusCode = 400
    ): JsonResponse {
        $response = [
            'code' => $code,
            'message' => [
                'en' => __($messageKey, $messageParams, 'en'),
                'ru' => __($messageKey, $messageParams, 'ru'),
            ],
        ];

        if ($errorDetails !== null) {
            $response['error'] = $errorDetails;
        }

        return response()->json($response, $statusCode);
    }
}

