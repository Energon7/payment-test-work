<?php

namespace App\Infrastructure\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

interface Response
{
    public function createErrorResponse(string $error, int $code, int $httpCode): JsonResponse;

    public function createSuccessResponse(string $data, int $code = 200, int $httpCode = 200): JsonResponse;
}
