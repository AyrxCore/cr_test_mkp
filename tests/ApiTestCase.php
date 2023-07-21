<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase as ApiPlatformTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\DataFixtures\Factory\UserFactory;
use App\DataFixtures\Story\UserStory;
use App\Tests\Constraint\MatchesJson;

class ApiTestCase extends ApiPlatformTestCase
{
    public const DEFAULT_USER_LOGIN = 'gsm@qantis.co';
    private const DEFAULT_USER_PASSWORD = '23AP4DF8';

    public function assertJsonResponseMatches($jsonFilePath, string $message = ''): void
    {
        self::assertThatForResponse(new MatchesJson($jsonFilePath), $message);
    }

    protected static function createClientWithCredentials(
        string $username = self::DEFAULT_USER_LOGIN,
        string $password = self::DEFAULT_USER_PASSWORD
    ): Client {
        // load dev fixtures
        UserStory::load();

        $client = self::createClient();

        $client->request('POST', '/api/authentication/token', [
            'json' => [
                'username' => $username,
                'password' => $password,
            ],
        ]);

        $user = UserFactory::find(['username' => $username]);
        if (\in_array('ROLE_API', $user->getRoles(), true)) {
            return $client;
        }

        $accounts = $user->getAccounts();
        if ($accounts->isEmpty()) {
            throw new \RuntimeException('Cannot create client. User has no account');
        }

        // select an account to initialize the session
        $client->request('GET', "/api/user/account/{$accounts->first()->getId()}/select");

        return $client;
    }
}
