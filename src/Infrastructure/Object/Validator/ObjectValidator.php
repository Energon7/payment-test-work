<?php

declare(strict_types=1);

namespace App\Infrastructure\Object\Validator;

use App\Infrastructure\Exception\Validation\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ObjectValidator
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    /**
     * @throws ValidationException
     */
    public function validate(object|array $dto): void
    {
        $violations = $this->validator->validate($dto);

        if ($violations->count() > 0) {
            $violations = iterator_to_array($violations);
            $violation = reset($violations);
            throw new ValidationException(
                property: $violation->getPropertyPath(),
                message: (string)$violation->getMessage()
            );
        }
    }
}
