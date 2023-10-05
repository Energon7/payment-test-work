<?php

declare(strict_types=1);

namespace App\Infrastructure\Object;

use ReflectionProperty;

class Initialer
{
    /**
     * @throws \ReflectionException
     */
    public function isInitialized(object $object, string $propertyName): bool
    {
        $rp = new ReflectionProperty(get_class($object), $propertyName);
        return $rp->isInitialized($object);
    }
}
