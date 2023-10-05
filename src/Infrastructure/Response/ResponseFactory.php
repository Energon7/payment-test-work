<?php

namespace App\Infrastructure\Response;

class ResponseFactory
{
    public function createApiResponse(
        int $code = 200,
        ?string $message = null,
        ?string $error = null,
        array $data = []
    ): ApiResponse {
        return new ApiResponse(
            code: $code,
            message: $message,
            error: $error,
            data: $data
        );
    }
}
