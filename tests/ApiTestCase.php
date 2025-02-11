<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase as ApiPlatformTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\DataFixtures\Factory\UserFactory;
use App\Entity\Account;
use App\Entity\User;
use App\Tests\Constraint\MatchesJson;
use App\Tests\Story\Account\UserStory;
use App\Tests\Story\Channel\ChannelParameterStory;
use App\Tests\Story\Channel\ChannelStory;

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
        string $password = self::DEFAULT_USER_PASSWORD,
        string $channel = 'QANTIS_TEST',
    ): Client {
        // load dev fixtures
        UserStory::load();
        ChannelStory::load();
        ChannelParameterStory::load();

        $client = self::createClient(defaultOptions: [
            'headers' => [
                'X-Channel' => $channel,
            ],
        ]);

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

        // get the user's account matching current channel
        $account = self::getUserFirstAccount($user->object(), $channel);

        if (!$account) {
            return $client;
        }

        // select an account to initialize the session
        $client->request('GET', "/api/accounts/{$account->getId()}/select");

        return $client;
    }

    protected static function getUserFirstAccount(User $user, string $channel = 'QANTIS_TEST'): ?Account
    {
        // get the account matching the channel
        return $user->getAccounts()
            ->filter(function (Account $account) use ($channel) {
                return $account->getAdherent()->getChannel()->getCode() === $channel;
            })
            ->first() ?: null;
    }
}
