<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function json(
        mixed $data = null,
        int $statusCode = 200,
        string $message = "Success.",
    ): JsonResponse {
        return response()->json([
            "message" => $message,
            "status_code" => $statusCode,
            "data" => $data,
        ]);
    }
}
