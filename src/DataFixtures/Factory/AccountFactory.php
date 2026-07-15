<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\Entity\Account;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Account>
 */
final class AccountFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Account::class;
    }

    protected function defaults(): array
    {
        return [
            'acceptCGU' => self::faker()->boolean(),
            'createdAt' => self::faker()->dateTime(),
            'enabled' => false,
            'lastConnexion' => self::faker()->dateTime(),
            'updatedAt' => self::faker()->dateTime(),
            'djustUsername' => self::faker()->userName(),
            'djustPassword' => self::faker()->word(),
            'djustCustomerAccountId' => self::faker()->word(),
            'user' => UserFactory::createOne(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
