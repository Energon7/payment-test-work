<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

class NotActiveCompanyException extends BaseException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 406);
    }
}
