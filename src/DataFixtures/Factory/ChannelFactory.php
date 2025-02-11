<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\DataFixtures\Factory\Trait\BeforeInstantiateTrait;
use App\Entity\Channel;
use App\Repository\ChannelRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Channel>
 *
 * @method        Channel|Proxy                     create(array|callable $attributes = [])
 * @method static Channel|Proxy                     createOne(array $attributes = [])
 * @method static Channel|Proxy                     find(object|array|mixed $criteria)
 * @method static Channel|Proxy                     findOrCreate(array $attributes)
 * @method static Channel|Proxy                     first(string $sortedField = 'id')
 * @method static Channel|Proxy                     last(string $sortedField = 'id')
 * @method static Channel|Proxy                     random(array $attributes = [])
 * @method static Channel|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ChannelRepository|RepositoryProxy repository()
 * @method static Channel[]|Proxy[]                 all()
 * @method static Channel[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Channel[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Channel[]|Proxy[]                 findBy(array $attributes)
 * @method static Channel[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Channel[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class ChannelFactory extends ModelFactory
{
    use BeforeInstantiateTrait;

    private string $appEnv;

    public function __construct()
    {
        $this->appEnv = \getenv('APP_ENV');

        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'code' => self::faker()->regexify('[A-Z_]{3,20}'),
            'hostname' => self::faker()->domainName(),
            'name' => self::faker()->name(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate($this->convertIdAttributes())
            ->beforeInstantiate(function ($attributes) {
                // remove protocol and replace domain with a one finishing with ".local" in test and dev environments
                if (isset($attributes['hostname']) && \in_array($this->appEnv, ['dev', 'test'], true)) {
                    $attributes['hostname'] = \preg_replace(
                        '/^(?:https?:\/\/)?([a-z\-.]+)\.[a-z]+$/',
                        '$1.local',
                        $attributes['hostname']
                    );
                }

                return $attributes;
            });
    }

    protected static function getClass(): string
    {
        return Channel::class;
    }
}
