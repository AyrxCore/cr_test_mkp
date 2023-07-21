<?php

declare(strict_types=1);

use App\DataFixtures\Factory\AccountFactory;
use App\DataFixtures\Factory\AdherentFactory;
use App\DataFixtures\Factory\UserFactory;

\it('returns an error on collection operations', function (
    string $method,
    string $expectedResponse
) {
    $client = $this::createClientWithCredentials();

    $client->request($method, '/api/accounts');

    $this->assertResponseStatusCodeSame(404);
    $this->assertJsonResponseMatches($expectedResponse);
})
    ->with([
        'GET' => [
            'method' => 'GET',
            'expectedResponse' => 'account/get-collection-not-found-response.json',
        ],
        'POST' => [
            'method' => 'POST',
            'expectedResponse' => 'account/post-not-found-response.json',
        ],
    ])
    ->group('accounts');

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

    $user = UserFactory::find(['username' => $this::DEFAULT_USER_LOGIN]);
    $accounts = $user->getAccounts();
    $accountId = $accounts->first()->getId();

    $client->request('DELETE', \sprintf('/api/accounts/%s', $accountId));

    $this->assertResponseStatusCodeSame(405);
    $this->assertJsonResponseMatches('account/delete-not-allowed-response.json');
})->group('accounts');

\it('returns a 403 Forbidden when trying to GET an account which belongs to another user', function () {
    UserFactory::new([
        'username' => 'some_user',
        'password' => 'password',
        'isEnabled' => true,
        'accesMarketPlace' => true,
        'accounts' => [
            AccountFactory::new([
                'adherent' => AdherentFactory::new(),
                'isEnabled' => true,
            ]),
        ],
    ])->create();

    $client = $this::createClientWithCredentials('some_user', 'password');

    $user = UserFactory::find(['username' => $this::DEFAULT_USER_LOGIN]);
    $accounts = $user->getAccounts();
    $accountId = $accounts->first()->getId();

    $client->request('GET', \sprintf('/api/accounts/%s', $accountId));

    $this->assertResponseStatusCodeSame(403);
    $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
})->group('accounts');

\it('GETs an account', function ($username) {
    $client = $this::createClientWithCredentials(username: $username);

    $user = UserFactory::find(['username' => $this::DEFAULT_USER_LOGIN]);
    $accounts = $user->getAccounts();
    $accountId = $accounts->first()->getId();

    $client->request('GET', \sprintf('/api/accounts/%s', $accountId));

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonResponseMatches('account/get-account-success-response.json');
    $this->assertJsonContains([
        'id' => (string) $accountId,
        '_user' => ['id' => (string) $user->getId()],
    ]);
})
    ->with([
        'Account belongs to logged in user' => [
            'username' => 'gsm@qantis.co',
        ],
        "logged in user has role ROLE_API and can access another user' account" => [
            'username' => 'api_user',
        ],
    ])
    ->group('accounts');
