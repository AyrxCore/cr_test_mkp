<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\DataFixtures\Factory\Trait\BeforeInstantiateTrait;
use App\Entity\ChannelOption;
use App\Repository\ChannelOptionRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<ChannelOption>
 *
 * @method        ChannelOption|Proxy                     create(array|callable $attributes = [])
 * @method static ChannelOption|Proxy                     createOne(array $attributes = [])
 * @method static ChannelOption|Proxy                     find(object|array|mixed $criteria)
 * @method static ChannelOption|Proxy                     findOrCreate(array $attributes)
 * @method static ChannelOption|Proxy                     first(string $sortedField = 'id')
 * @method static ChannelOption|Proxy                     last(string $sortedField = 'id')
 * @method static ChannelOption|Proxy                     random(array $attributes = [])
 * @method static ChannelOption|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ChannelOptionRepository|RepositoryProxy repository()
 * @method static ChannelOption[]|Proxy[]                 all()
 * @method static ChannelOption[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static ChannelOption[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static ChannelOption[]|Proxy[]                 findBy(array $attributes)
 * @method static ChannelOption[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static ChannelOption[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class ChannelOptionFactory extends ModelFactory
{
    use BeforeInstantiateTrait;

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->word(),
            'value' => self::faker()->word(),
        ];
    }

    protected function initialize(): self
    {
        return $this->beforeInstantiate($this->convertIdAttributes());
    }

    protected static function getClass(): string
    {
        return ChannelOption::class;
    }
}
