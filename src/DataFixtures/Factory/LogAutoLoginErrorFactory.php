<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\DataFixtures\Factory\Trait\BeforeInstantiateTrait;
use App\Entity\LogAutoLoginError;
use App\Repository\LogAutoLoginErrorRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<LogAutoLoginError>
 *
 * @method        LogAutoLoginError|Proxy                     create(array|callable $attributes = [])
 * @method static LogAutoLoginError|Proxy                     createOne(array $attributes = [])
 * @method static LogAutoLoginError|Proxy                     find(object|array|mixed $criteria)
 * @method static LogAutoLoginError|Proxy                     findOrCreate(array $attributes)
 * @method static LogAutoLoginError|Proxy                     first(string $sortedField = 'id')
 * @method static LogAutoLoginError|Proxy                     last(string $sortedField = 'id')
 * @method static LogAutoLoginError|Proxy                     random(array $attributes = [])
 * @method static LogAutoLoginError|Proxy                     randomOrCreate(array $attributes = [])
 * @method static LogAutoLoginErrorRepository|RepositoryProxy repository()
 * @method static LogAutoLoginError[]|Proxy[]                 all()
 * @method static LogAutoLoginError[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static LogAutoLoginError[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static LogAutoLoginError[]|Proxy[]                 findBy(array $attributes)
 * @method static LogAutoLoginError[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static LogAutoLoginError[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class LogAutoLoginErrorFactory extends ModelFactory
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
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'email' => self::faker()->email(),
            'reason' => self::faker()->sentence(),
            'channelName' => self::faker()->word(),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return LogAutoLoginError::class;
    }
}
