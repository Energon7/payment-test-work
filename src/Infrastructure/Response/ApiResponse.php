<?php

namespace App\Infrastructure\Response;

class ApiResponse implements \JsonSerializable
{
    public function __construct(
        private int $code = 200,
        private ?string $message = null,
        private ?string $error = null,
        private array $data = []
    ) {
    }

    public function jsonSerialize(): array
    {
        $response = array_filter([
            'code' => $this->code,
            'message' => $this->message,
            'error' => $this->error,
        ]);
        $response['data'] = $this->data;
        return $response;
    }
}
