<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer;

use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BackedEnumNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     *
     * @return int|string
     */
    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        if (!$object instanceof \BackedEnum) {
            throw new InvalidArgumentException('The data must belong to a backed enumeration.');
        }

        return $object->value;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof \BackedEnum;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotNormalizableValueException
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        if (!is_subclass_of($type, \BackedEnum::class)) {
            throw new InvalidArgumentException('The data must belong to a backed enumeration.');
        }

        if (!\is_int($data) && !\is_string($data)) {
            // phpcs:ignore
            throw NotNormalizableValueException::createForUnexpectedDataType('The data is neither an integer nor a string, you should pass an integer or a string that can be parsed as an enumeration case of type ' . $type . '.', $data, [Type::BUILTIN_TYPE_INT, Type::BUILTIN_TYPE_STRING], $context['deserialization_path'] ?? null, true);
        }

        try {
            return $type::from($data);
        } catch (\ValueError $e) {
            // phpcs:ignore
            throw NotNormalizableValueException::createForUnexpectedDataType($e->getMessage(), $data, [Type::BUILTIN_TYPE_INT, Type::BUILTIN_TYPE_STRING], $context['deserialization_path'] ?? null, true, $e->getCode(), $e);
        }
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return is_subclass_of($type, \BackedEnum::class);
    }

    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
