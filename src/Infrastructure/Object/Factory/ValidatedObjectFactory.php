<?php

namespace App\Infrastructure\Object\Factory;

use App\Infrastructure\Exception\Validation\ValidationException;
use App\Infrastructure\Object\Denormalizer\Denormalizer;
use App\Infrastructure\Object\Denormalizer\Exception\FailedDenormalizationException;
use App\Infrastructure\Object\Validator\ObjectValidator;

class ValidatedObjectFactory
{
    public function __construct(
        private readonly ObjectValidator $objectValidator,
        private readonly Denormalizer $denormalizer
    ) {
    }

    /**
     * @psalm-template T
     * @param class-string<T> $classType
     * @return T
     * @throws ValidationException
     */
    public function strictCreateObject(array $data, string $classType): object
    {
        try {
            $object = $this->denormalizer->strictObjectDenormalize($data, $classType);
            $this->objectValidator->validate($object);
            return $object;
        } catch (FailedDenormalizationException $failedDenormalizationException) {
            $messages = $failedDenormalizationException->getMessages();
            $message = reset($messages);
            throw new ValidationException(null, $message);
        }
    }

    /**
     * @psalm-template T
     * @param class-string<T> $classType
     * @return T[]
     * @throws ValidationException
     */
    public function strictCreateObjectsArray(array $data, string $classType): array
    {
        try {
            $arrayOfObjects = $this->denormalizer->strictArrayDenormalize($data, $classType);
            $this->objectValidator->validate($arrayOfObjects);
            return $arrayOfObjects;
        } catch (FailedDenormalizationException $failedDenormalizationException) {
            $messages = $failedDenormalizationException->getMessages();
            $message = reset($messages);
            throw new ValidationException(null, $message);
        }
    }
}
