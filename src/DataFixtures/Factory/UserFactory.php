<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return User::class;
    }

    protected function createEmail(): string
    {
        return self::faker()->unique()->safeEmail();
    }

    protected function defaults(): array
    {
        $email = $this->createEmail();

        return [
            'email' => $email,
            'password' => self::faker()->password(10),
            'username' => $email,
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'enabled' => false,
            'roles' => [],
        ];
    }

    protected function initialize(): static
    {
        return $this->afterInstantiate(function (User $user) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        });
    }
}
