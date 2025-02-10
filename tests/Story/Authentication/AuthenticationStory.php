<?php

declare(strict_types=1);

namespace App\Tests\Story\Authentication;

use App\DataFixtures\Factory\AccountFactory;
use App\DataFixtures\Factory\UserFactory;
use App\DataFixtures\Story\UserStory;
use Zenstruck\Foundry\Story;

/**
 * @method static AuthenticationStory userWithoutMarketplaceAccess()
 */
final class AuthenticationStory extends Story
{
    public function build(): void
    {
        UserStory::load();

        $users = UserFactory::new()
            ->sequence([
                [
                    'email' => 'test_role_api_user_disabled@qantis.co',
                    'username' => 'test_role_api_user_disabled',
                    'password' => '000000',
                    'firstName' => 'test_role_api_user_disabled',
                    'lastName' => 'TEST',
                    'enabled' => false,
                    'roles' => ['ROLE_API'],
                ],
                [
                    'email' => 'test_user_without_account@qantis.co',
                    'username' => 'test_user_without_account',
                    'password' => '000000',
                    'firstName' => 'test_user_without_account',
                    'lastName' => 'TEST',
                    'enabled' => true,
                    'roles' => [],
                ],
                [
                    'email' => 'test_user_with_disabled_account@qantis.co',
                    'username' => 'test_user_with_disabled_account',
                    'password' => '000000',
                    'firstName' => 'test_user_with_disabled_account',
                    'lastName' => 'TEST',
                    'enabled' => true,
                    'accounts' => [
                    ],
                ],
            ])
            ->create();
        AccountFactory::createOne([
            'user' => $users[2],
        ]);
    }
}
