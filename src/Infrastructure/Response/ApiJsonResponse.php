<?php

namespace App\Infrastructure\Response;

use App\Infrastructure\Response\Response as ApiResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ApiJsonResponse implements ApiResponse
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createErrorResponse(string $error, int $code, int $httpCode, array $data = []): JsonResponse
    {
        return new JsonResponse([
            'code' => $code,
            'error' => $error,
            'data' => $data
        ], $httpCode);
    }

    public function createSuccessResponse(string $data, int $code = 200, int $httpCode = 200): JsonResponse
    {
        $response = $this->serializer->serialize($data, 'json');
        return new JsonResponse(
            [
                'code' => $code,
                'message' => $this->translator->trans(\str_replace('"', "", $response))
            ],
            $httpCode
        );
    }
}
