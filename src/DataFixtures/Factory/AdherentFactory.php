<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\Entity\Adherent;
use App\Repository\AdherentRepository;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Adherent>
 *
 * @method        Adherent|Proxy                     create(array|callable $attributes = [])
 * @method static Adherent|Proxy                     createOne(array $attributes = [])
 * @method static Adherent|Proxy                     find(object|array|mixed $criteria)
 * @method static Adherent|Proxy                     findOrCreate(array $attributes)
 * @method static Adherent|Proxy                     first(string $sortedField = 'id')
 * @method static Adherent|Proxy                     last(string $sortedField = 'id')
 * @method static Adherent|Proxy                     random(array $attributes = [])
 * @method static Adherent|Proxy                     randomOrCreate(array $attributes = [])
 * @method static AdherentRepository|RepositoryProxy repository()
 * @method static Adherent[]|Proxy[]                 all()
 * @method static Adherent[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Adherent[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Adherent[]|Proxy[]                 findBy(array $attributes)
 * @method static Adherent[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Adherent[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 *
 * TODO: rename Adherent to Member
 */
final class AdherentFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'id' => self::faker()->uuid(),
            'name' => self::faker()->name(),
            'reducceCode' => self::faker()->regexify('[0-9a-zA-Z]{10}'),
            'siret' => self::faker()->regexify('[0-9]{14}'),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                if (\is_string($attributes['id'])) {
                    $attributes['id'] = Uuid::fromString($attributes['id']);
                }

                return $attributes;
            });
    }

    protected static function getClass(): string
    {
        return Adherent::class;
    }
}
