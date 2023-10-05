<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception\Validation;

use App\Infrastructure\Exception\BaseException;

class ValidationException extends BaseException
{
    /**
     * @var ValidationError[]
     */
    private array $errors;

    public function __construct(?string $property, string $message)
    {
        parent::__construct(code: 400);
        $this->addError(new ValidationError($property, $message));
    }

    public function addError(ValidationError $error): void
    {
        $this->errors[] = $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
