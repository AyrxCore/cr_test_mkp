<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Account>
 *
 * @method        Account|Proxy                     create(array|callable $attributes = [])
 * @method static Account|Proxy                     createOne(array $attributes = [])
 * @method static Account|Proxy                     find(object|array|mixed $criteria)
 * @method static Account|Proxy                     findOrCreate(array $attributes)
 * @method static Account|Proxy                     first(string $sortedField = 'id')
 * @method static Account|Proxy                     last(string $sortedField = 'id')
 * @method static Account|Proxy                     random(array $attributes = [])
 * @method static Account|Proxy                     randomOrCreate(array $attributes = [])
 * @method static AccountRepository|RepositoryProxy repository()
 * @method static Account[]|Proxy[]                 all()
 * @method static Account[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Account[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Account[]|Proxy[]                 findBy(array $attributes)
 * @method static Account[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Account[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class AccountFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'acceptCGU' => self::faker()->boolean(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'isEnabled' => false,
            'lastConnexion' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'updatedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'upplerClientId' => self::faker()->regexify('[0-9]{3,4}_[0-9a-z]{50}'),
            'upplerClientSecret' => self::faker()->regexify('[0-9a-z]{50}'),
            'upplerCompanyId' => self::faker()->randomNumber(3),
            'upplerPassword' => self::faker()->password(10),
            'upplerSubAccountId' => self::faker()->randomNumber(3),
            'upplerUserId' => self::faker()->randomNumber(4),
            'upplerUsername' => self::faker()->userName(),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Account::class;
    }
}
