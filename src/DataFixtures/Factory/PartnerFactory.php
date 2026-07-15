<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\DataFixtures\Factory\Trait\BeforeInstantiateTrait;
use App\Entity\Partner;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class PartnerFactory extends PersistentObjectFactory
{
    use BeforeInstantiateTrait;

    public static function class(): string
    {
        return Partner::class;
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->company(),
            'created_at' => new \DateTimeImmutable(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
