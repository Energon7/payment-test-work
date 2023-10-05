<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use Exception;

class UniqueConstraintViolationException extends BaseException
{
    public string $property;
    public string $value;

    private function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->property = '';
        $this->value = '';
    }

    public static function create(string $property, string $value): self
    {
        $exception = new self(\sprintf('%s already exists with value \'%s\'', $property, $value));
        $exception->property = $property;
        $exception->value = $value;

        return $exception;
    }
}
