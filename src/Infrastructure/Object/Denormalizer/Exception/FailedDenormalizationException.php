<?php

namespace App\Infrastructure\Object\Denormalizer\Exception;

use App\Infrastructure\Exception\BaseException;

class FailedDenormalizationException extends BaseException
{
    private const EXCEPTION_CODE = 422;

    private array $messages;

    public function __construct(array $messages, \Throwable $previous = null)
    {
        parent::__construct(code: self::EXCEPTION_CODE, previous: $previous);
        $this->messages = $messages;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
