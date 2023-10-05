<?php

declare(strict_types=1);

namespace App\Infrastructure\Object\Denormalizer;

use App\Infrastructure\Object\Denormalizer\Exception\FailedDenormalizationException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Denormalizer
{
    private const ENUM_VALIDATION_MESSAGE_PART = 'is not a valid backing value for enum';
    public const IGNORE_ATTRIBUTES = AbstractNormalizer::IGNORED_ATTRIBUTES;

    public function __construct(private readonly DenormalizerInterface $denormalizer)
    {
    }

    /**
     * @psalm-template T
     * @param class-string<T> $classType
     * @return T
     * @throws FailedDenormalizationException
     * @psalm-suppress InvalidReturnType
     */
    public function strictObjectDenormalize(array $data, string $classType, array $context = []): object
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @psalm-suppress InvalidReturnStatement
         */
        return $this->strictDenormalize($data, $classType, $context);
    }

    /**
     * @psalm-template T
     * @param class-string<T> $classType
     * @return T[]
     * @throws FailedDenormalizationException
     * @psalm-suppress InvalidReturnType
     */
    public function strictArrayDenormalize(array $data, string $classType): array
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @psalm-suppress InvalidReturnStatement
         */
        return $this->strictDenormalize($data, $classType . '[]');
    }

    /**
     * @psalm-template T
     * @param class-string<T> $classType
     * @return T|T[]
     * @throws FailedDenormalizationException
     */
    private function strictDenormalize(array $data, string $classType, array $context = []): object|array
    {
        $context[DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS] = true;
        try {
            return $this->denormalizer->denormalize(
                $data,
                $classType,
                context: $context
            );
        } catch (PartialDenormalizationException $exception) {
            $denormalizetionError = [];
            /** @var NotNormalizableValueException $error */
            foreach ($exception->getErrors() as $error) {
                if (is_null($path = $error->getPath())) {
                    throw new FailedDenormalizationException([$error->getMessage()]);
                }

                if (stripos($error->getMessage(), self::ENUM_VALIDATION_MESSAGE_PART) !== false) {
                    $message = 'Enum values error';
                } else {
                    $expectTypes = $error->getExpectedTypes() ?? ['unknown'];
                    $currentType = $error->getCurrentType() ?? 'unknown';
                    $message = sprintf(
                        'The type must be one of "%s" ("%s" given).',
                        implode(', ', $expectTypes),
                        $currentType
                    );
                }
                $denormalizetionError[$path] = $message;
                throw new FailedDenormalizationException($denormalizetionError);
            }
            throw new FailedDenormalizationException(['Failed denormalize']);
        } catch (\Throwable $throwable) {
            throw new FailedDenormalizationException(['Invalid property' => $throwable->getMessage()]);
        }
    }

    /**
     * @param array $data
     * @param object $object
     * @return void
     * @throws FailedDenormalizationException
     */
    public function typeEnforcementDenormalizeOnObject(array $data, object $object, array $context = []): void
    {
        $context[AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT] = true;
        $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $object;
        try {
            $this->denormalizer->denormalize(
                $data,
                $object::class,
                context: $context
            );
        } catch (NotNormalizableValueException $exception) {
            // Temporary solution. At DISABLE_TYPE_ENFORCEMENT in NotNormalizableValueException is not path
            // TODO Parse message for extract property path and correct message
            $messages = explode(':', $exception->getMessage());
            $path = $exception->getPath() ?? 'invalidProperty' ;
            throw new FailedDenormalizationException([$path => trim($messages[1] ?? $messages[0])]);
        } catch (\Throwable $throwable) {
            throw new FailedDenormalizationException(['invalidProperty' => $throwable->getMessage()]);
        }
    }
}
