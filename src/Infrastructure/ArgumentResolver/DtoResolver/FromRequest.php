<?php

declare(strict_types=1);

namespace App\Infrastructure\ArgumentResolver\DtoResolver;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS)]
class FromRequest
{
    public function __construct(private array $ignore = [])
    {
    }

    /**
     * @return array
     */
    public function getIgnore(): array
    {
        return $this->ignore;
    }
}
