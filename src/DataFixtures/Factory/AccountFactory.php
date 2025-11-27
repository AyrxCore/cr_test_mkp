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
            'upplerClientId' => self::faker()->regexify('[0-9]{3,4}_[0-9a-z]{50}'),
            'upplerClientSecret' => self::faker()->regexify('[0-9a-z]{50}'),
            'upplerCompanyId' => self::faker()->randomNumber(3),
            'upplerPassword' => self::faker()->password(10),
            'upplerSubAccountId' => self::faker()->randomNumber(3),
            'upplerUserId' => self::faker()->randomNumber(4),
            'upplerUsername' => self::faker()->userName(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
