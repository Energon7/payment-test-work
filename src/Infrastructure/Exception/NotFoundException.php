<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

class NotFoundException extends BaseException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 404);
    }

    public static function createWithNameAndId(string $name, string|int $id): self
    {
        return new self(sprintf('%s with id %s not found', $name, $id));
    }
}
