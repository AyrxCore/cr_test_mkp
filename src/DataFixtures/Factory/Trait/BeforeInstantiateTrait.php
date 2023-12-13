<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory\Trait;

use Symfony\Component\Uid\Uuid;

trait BeforeInstantiateTrait
{
    protected function convertIdAttributes(): \Closure
    {
        return function (array $attributes): array {
            if (isset($attributes['id']) && \is_string($attributes['id'])) {
                $attributes['id'] = Uuid::fromString($attributes['id']);
            }

            return $attributes;
        };
    }
}
