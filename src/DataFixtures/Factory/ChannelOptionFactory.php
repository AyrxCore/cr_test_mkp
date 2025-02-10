<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\DataFixtures\Factory\Trait\BeforeInstantiateTrait;
use App\Entity\ChannelOption;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<ChannelOption>
 */
final class ChannelOptionFactory extends PersistentObjectFactory
{
    use BeforeInstantiateTrait;

    public static function class(): string
    {
        return ChannelOption::class;
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->word(),
            'value' => self::faker()->word(),
        ];
    }

    protected function initialize(): static
    {
        return $this->beforeInstantiate($this->convertIdAttributes());
    }
}
