<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\DataFixtures\Factory\Trait\BeforeInstantiateTrait;
use App\Entity\Accord;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class AccordFactory extends PersistentObjectFactory
{
    use BeforeInstantiateTrait;

    public static function class(): string
    {
        return Accord::class;
    }

    protected function defaults(): array
    {
        return [
            'id' => self::faker()->uuid(),
            'name' => self::faker()->name(),
            'partner' => PartnerFactory::new(),
            'created_at' => new \DateTimeImmutable(),
            'logo' => 'https://picsum.photos/seed/200/300',
        ];
    }

    protected function initialize(): static
    {
        return $this->beforeInstantiate($this->convertIdAttributes());
    }
}
