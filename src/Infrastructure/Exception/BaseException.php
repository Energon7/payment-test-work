<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class BaseException extends \Exception
{
    private array $payload = [];

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     */
    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }
}
