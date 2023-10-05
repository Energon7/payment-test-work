<?php

namespace App\Transport\Http\Api;

use App\Infrastructure\Response\ResponseFactory;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;


class BaseApiController extends AbstractController
{
    public function apiJsonResponse(
        int $code = 200,
        ?string $message = null,
        ?string $error = null,
        array $data = [],
    ): JsonResponse {
        $factory = new ResponseFactory();
        return new JsonResponse(
            data: $factory->createApiResponse(
                code: $code,
                message: $message,
                error: $error,
                data: $this->normalizeData($data)
            )
        );
    }

    private function normalizeData(array $data): array
    {
        foreach ($data as &$row) {
            if (is_array($row)) {
                $row = $this->normalizeData($row);
                continue;
            }
            if ($row instanceof \DateTimeInterface) {
                $row = $row->format(DATE_ATOM);
            }
        }
        return $data;
    }
}
