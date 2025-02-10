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
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ApiTestCase extends ApiPlatformTestCase
{
    use Factories;
    use ResetDatabase;

    public const string DEFAULT_USER_LOGIN = 'gsm@qantis.co';
    protected const string DEFAULT_USER_PASSWORD = '23AP4DF8';

    public function assertJsonResponseMatches($jsonFilePath, string $message = ''): void
    {
        self::assertThatForResponse(new MatchesJson($jsonFilePath), $message);
    }

    protected static function createClientWithCredentials(
        string $username = UserStory::DEFAULT_USER,
        string $password = UserStory::DEFAULT_PASSWORD,
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

        $accounts = $user->getAccounts();

        $account = $accounts->first();
        if (!$account) {
            return $client;
        }

        // select an account to initialize the session
        $client->request('GET', "/api/accounts/{$account->getId()}/select");

        return $client;
    }

    // get the user's account matching current channel
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
