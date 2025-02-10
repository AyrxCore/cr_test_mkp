<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\DataFixtures\Factory\Trait\BeforeInstantiateTrait;
use App\Entity\LogAutoLoginError;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<LogAutoLoginError>
 */
final class LogAutoLoginErrorFactory extends PersistentObjectFactory
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
        return LogAutoLoginError::class;
    }

    protected function defaults(): array
    {
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'email' => self::faker()->email(),
            'reason' => self::faker()->sentence(),
            'channelName' => self::faker()->word(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
