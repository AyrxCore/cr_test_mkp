<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\DataFixtures\Factory\Trait\BeforeInstantiateTrait;
use App\Entity\Adherent;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class AdherentFactory extends PersistentObjectFactory
{
    use BeforeInstantiateTrait;

    public static function class(): string
    {
        return Adherent::class;
    }

    protected function defaults(): array
    {
        return [
            'id' => self::faker()->uuid(),
            'name' => self::faker()->name(),
            'reducceCode' => self::faker()->regexify('[0-9a-zA-Z]{10}'),
            'siret' => self::faker()->regexify('[0-9]{14}'),
            'logo' => self::faker()->imageUrl(),
        ];
    }

    protected function initialize(): static
    {
        return $this->beforeInstantiate($this->convertIdAttributes());
    }
}
