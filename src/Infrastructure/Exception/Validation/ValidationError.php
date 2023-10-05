<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception\Validation;

class ValidationError
{
    public function __construct(
        private readonly ?string $property,
        private readonly string $message
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function getFormatMessage(): string
    {
        if ($this->property) {
            return sprintf('%s: %s', $this->property, $this->message);
        }
        return sprintf('%s', $this->message);
    }
}
