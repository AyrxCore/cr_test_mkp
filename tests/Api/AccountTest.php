<?php

declare(strict_types=1);

use App\DataFixtures\Factory\AccountFactory;
use App\DataFixtures\Factory\UserFactory;
use App\Tests\Story\Account\UserStory;

\it('returns an error on collection operations', function () {
    $client = $this::createClientWithCredentials();

    $client->request('POST', '/api/accounts');

    $this->assertResponseStatusCodeSame(405);
    $this->assertJsonResponseMatches('account/post-not-found-response.json');
})->group('accounts');

\it('returns a 405 Method Not Allowed when trying to PATCH an account', function () {
    $client = $this::createClientWithCredentials();

    $client->request('PATCH', '/api/accounts/8e211f4d-c2c9-4fdb-9031-5414d019b488');

    $this->assertResponseStatusCodeSame(405);
    $this->assertJsonResponseMatches('account/patch-not-allowed-response.json');
})->group('accounts');

\it('returns a 404 Not Found when trying to GET a non-existent account', function () {
    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/accounts/8e211f4d-c2c9-4fdb-9031-5414d019b488');

    $this->assertResponseStatusCodeSame(404);
    $this->assertJsonContains(['hydra:description' => 'Not Found']);
})->group('accounts');

\it('returns a 405 Method Not Allowed when trying to DELETE an account', function () {
    $client = $this::createClientWithCredentials();

    $user = UserFactory::find(['username' => UserStory::DEFAULT_USER]);
    $accounts = $user->getAccounts();
    $accountId = $accounts->first()->getId();

    $client->request('DELETE', \sprintf('/api/accounts/%s', $accountId));

    $this->assertResponseStatusCodeSame(405);
    $this->assertJsonResponseMatches('account/delete-not-allowed-response.json');
})->group('accounts');

\it('returns a 401 Forbidden when trying to GET an account which belongs to another user', function () {
    $user = UserFactory::new([
        'username' => 'some_user',
        'password' => 'password',
        'enabled' => true,
        'roles' => ['ROLE_USER'],
    ])->create();

    AccountFactory::new([
        'adherent' => UserStory::adherentQantisTest(),
        'enabled' => true,
        'user' => $user,
    ]);

    $client = $this::createClientWithCredentials('some_user', 'password');

    $user = UserFactory::find(['username' => UserStory::DEFAULT_USER]);
    $accountId = $this::getUserFirstAccount($user)->getId();

    $client->request('GET', \sprintf('/api/accounts/%s', $accountId));

    $this->assertResponseStatusCodeSame(401);
    $this->assertJsonContains(['message' => 'JWT Token not found']);
})->group('accounts');

\it('GETs a collection of accounts logged in user has role ROLE_API and can access another user\'s accounts', function () {
    $client = $this::createClientWithCredentials(username: 'api_user');

    $client->request('GET', '/api/accounts');

    $this->assertResponseStatusCodeSame(200);
})->group('accounts');

\it('GETs a collection of accounts Account belongs to logged in user', function () {
    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/accounts');

    $this->assertResponseStatusCodeSame(200);

    $this->assertJsonResponseMatches('account/get-collection-response.json');
})->group('accounts');

\it('GETs an account', function (string $loggedInUsername, string $otherUsername) {
    $client = $this::createClientWithCredentials(username: $loggedInUsername);

    $user = UserFactory::find(['username' => $otherUsername]);
    $accountId = $this::getUserFirstAccount($user)->getId();

    $client->request('GET', \sprintf('/api/accounts/%s', $accountId));

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonResponseMatches('account/get-account-success-response.json');
    $this->assertJsonContains([
        'id' => (string) $accountId,
        'user' => ['id' => (string) $user->getId()],
    ]);
})
    ->with([
        'Account belongs to logged in user' => [
            'loggedInUsername' => UserStory::DEFAULT_USER,
            'otherUsername' => UserStory::DEFAULT_USER,
        ],
        "logged in user has role ROLE_API and can access another user's account" => [
            'loggedInUsername' => 'api_user',
            'otherUsername' => UserStory::DEFAULT_USER,
        ],
    ])
    ->group('accounts');
