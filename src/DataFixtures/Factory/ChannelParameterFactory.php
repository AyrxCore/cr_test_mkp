<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\DataFixtures\Factory\Trait\BeforeInstantiateTrait;
use App\Entity\ChannelParameter;
use App\Repository\ChannelParameterRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<ChannelParameter>
 *
 * @method        ChannelParameter|Proxy                     create(array|callable $attributes = [])
 * @method static ChannelParameter|Proxy                     createOne(array $attributes = [])
 * @method static ChannelParameter|Proxy                     find(object|array|mixed $criteria)
 * @method static ChannelParameter|Proxy                     findOrCreate(array $attributes)
 * @method static ChannelParameter|Proxy                     first(string $sortedField = 'id')
 * @method static ChannelParameter|Proxy                     last(string $sortedField = 'id')
 * @method static ChannelParameter|Proxy                     random(array $attributes = [])
 * @method static ChannelParameter|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ChannelParameterRepository|RepositoryProxy repository()
 * @method static ChannelParameter[]|Proxy[]                 all()
 * @method static ChannelParameter[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static ChannelParameter[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static ChannelParameter[]|Proxy[]                 findBy(array $attributes)
 * @method static ChannelParameter[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static ChannelParameter[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class ChannelParameterFactory extends ModelFactory
{
    use BeforeInstantiateTrait;

    protected function getDefaults(): array
    {
        return [
            'logo' => self::faker()->imageUrl(width: 160, height: 60),
            'favicon' => self::faker()->imageUrl(width: 16, height: 16),
            'email' => self::faker()->email(),
            'phoneNumber' => self::faker()->e164PhoneNumber(),
            'legalTerms' => self::faker()->url(),
            'generalTermsOfUse' => self::faker()->url(),
            'privacyPolicy' => self::faker()->url(),
            'primaryColor' => self::faker()->hexColor(),
            'secondaryColor' => self::faker()->hexColor(),
            'textColor' => self::faker()->hexColor(),
            'whiteLabel' => true,
        ];
    }

    protected function initialize(): self
    {
        return $this->beforeInstantiate($this->convertIdAttributes());
    }

    protected static function getClass(): string
    {
        return ChannelParameter::class;
    }
}
