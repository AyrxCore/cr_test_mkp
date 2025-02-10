<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\DataFixtures\Factory\Trait\BeforeInstantiateTrait;
use App\Entity\Channel;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Channel>
 */
final class ChannelFactory extends PersistentObjectFactory
{
    use BeforeInstantiateTrait;

    private string $appEnv;

    public function __construct()
    {
        $this->appEnv = \getenv('APP_ENV');

        parent::__construct();
    }

    public static function class(): string
    {
        return Channel::class;
    }

    protected function defaults(): array
    {
        return [
            'code' => self::faker()->regexify('[A-Z_]{3,20}'),
            'hostname' => self::faker()->domainName(),
            'name' => self::faker()->name(),
        ];
    }

    protected function initialize(): static
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
}
