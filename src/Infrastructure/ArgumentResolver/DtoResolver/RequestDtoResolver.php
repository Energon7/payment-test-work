<?php

declare(strict_types=1);

namespace App\Infrastructure\ArgumentResolver\DtoResolver;

use App\Infrastructure\Exception\Validation\ValidationException;
use App\Infrastructure\Object\Denormalizer\Denormalizer;
use App\Infrastructure\Object\Denormalizer\Exception\FailedDenormalizationException;
use App\Infrastructure\Object\Validator\ObjectValidator;
use Generator;
use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RequestDtoResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private readonly ObjectValidator $validator,
        private readonly Denormalizer $denormalizer
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $type = $argument->getType();
        if ($type === null || !class_exists($type)) {
            return false;
        }

        if (count($argument->getAttributes(FromRequest::class)) > 0) {
            return true;
        }

        $class = new ReflectionClass($type);
        $attributes = $class->getAttributes(FromRequest::class, ReflectionAttribute::IS_INSTANCEOF);

        return count($attributes) > 0;
    }

    /**
     * @throws ValidationException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        try {
            $classType = $argument->getType();
            assert($classType !== null && class_exists($classType));

            $denormalizeContext = [];
            /** @var FromRequest $fromRequest */
            foreach ($argument->getAttributes(FromRequest::class) as $fromRequest) {
                if ($fromRequest->getIgnore()) {
                    $denormalizeContext[Denormalizer::IGNORE_ATTRIBUTES] = $fromRequest->getIgnore();
                }
            }
            $dto = $this->denormalizer->strictObjectDenormalize(
                data: $this->getRequestData($request),
                classType: $classType,
                context: $denormalizeContext
            );

            $queryAndAttributesData = $this->getQueryAndAttributesData($request);
            if (!empty($queryAndAttributesData)) {
                $this->denormalizer->typeEnforcementDenormalizeOnObject(
                    $queryAndAttributesData,
                    $dto,
                    $denormalizeContext
                );
            }

            $this->validator->validate($dto);
            yield $dto;
        } catch (FailedDenormalizationException $exception) {
            $messages = $exception->getMessages();
            $property = array_key_first($messages);
            throw new ValidationException(
                is_string($property) ? $property : null,
                $messages[$property] ?? ''
            );
        }
    }

    private function getRequestData(Request $request): array
    {
        $dataStrictDenormalize = array_merge($request->request->all(), $request->files->all());

        if ('json' === $request->getContentType()) {
            $dataStrictDenormalize = array_merge($dataStrictDenormalize, $request->toArray());
        }

        return $dataStrictDenormalize;
    }

    private function getQueryAndAttributesData(Request $request): array
    {
        /** @var array $routeParams */
        $routeParams = $request->attributes->get('_route_params', []);
        $queryParams = $request->query->all();

        return array_merge($queryParams, $routeParams);
    }
}
