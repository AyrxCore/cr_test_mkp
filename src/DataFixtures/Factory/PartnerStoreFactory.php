<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\DataFixtures\Factory\Trait\BeforeInstantiateTrait;
use App\Entity\PartnerStore;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class PartnerStoreFactory extends PersistentObjectFactory
{
    use BeforeInstantiateTrait;

    public static function class(): string
    {
        return PartnerStore::class;
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->company(),
            'partner' => PartnerFactory::new(),
            'address' => self::faker()->streetAddress(),
            'latitude' => self::faker()->latitude(),
            'longitude' => self::faker()->longitude(),
            'phone' => self::faker()->phoneNumber(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
