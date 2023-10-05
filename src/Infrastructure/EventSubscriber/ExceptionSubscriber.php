<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSubscriber;

use App\Infrastructure\Exception\BaseException;
use App\Infrastructure\Exception\NotFoundException;
use App\Infrastructure\Exception\Validation\ValidationException;
use App\Infrastructure\Response\ApiJsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

/**
 * Class ExceptionSubscriber
 *
 * @package App\Infrastructure\Service\EventSubscriber
 */
class ExceptionSubscriber implements EventSubscriberInterface
{
    private ApiJsonResponse $apiResponse;
    private LoggerInterface $logger;

    public function __construct(ApiJsonResponse $apiResponse, LoggerInterface $logger, readonly string $environment)
    {
        $this->apiResponse = $apiResponse;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => [
                ['onApiKernelException', -100]
            ],
        ];
    }

    /**
     * Method to handle kernel exception.
     *
     * @param ExceptionEvent $event
     *
     */
    public function onApiKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $response = $this->apiResponse->createErrorResponse(
                error: $exception->getMessage(),
                code: $exception->getCode(),
                httpCode: $exception->getStatusCode()
            );
            $event->setResponse($response);
            return;
        }

        $response = new JsonResponse();

        $exception = $this->getMainExceptionFromBus($exception);

        if ($exception instanceof ValidationException) {
            $response = $this->apiResponse->createErrorResponse(
                error: ($exception->getErrors()[0] ?? null)?->getFormatMessage() ?? $exception->getMessage(),
                code: $exception->getCode(),
                httpCode: 422
            );
        } elseif ($exception instanceof NotFoundException) {
            $response = $this->apiResponse->createErrorResponse(
                error: $exception->getMessage(),
                code: $exception->getCode(),
                httpCode: 404,
                data: $exception->getPayload()
            );
        } elseif ($exception instanceof BaseException) {
            $response = $this->apiResponse->createErrorResponse(
                $exception->getMessage(),
                $exception->getCode(),
                400,
                data: $exception->getPayload()
            );
        } else {
            $response = $this->apiResponse->createErrorResponse(
                $this->environment === 'prod' ? 'Internal server error' : $exception->getMessage(),
                500,
                500
            );
        }

        $event->setResponse($response);
    }

    private function getMainExceptionFromBus(\Throwable $throwable): \Throwable
    {
        if ($throwable instanceof HandlerFailedException) {
            $previous = $throwable->getPrevious();
            if ($previous === null) {
                return $throwable;
            }
            return $this->getMainExceptionFromBus($previous);
        }
        return $throwable;
    }
}
