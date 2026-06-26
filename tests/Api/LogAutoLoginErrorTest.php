<?php

declare(strict_types=1);

use App\DataFixtures\Factory\UserFactory;
use App\Entity\LogAutoLoginError;
use App\Tests\Story\Account\UserStory;
use App\Tests\Story\LogAutoLoginError\LogAutoLoginErrorStory;

\beforeEach(function () {
    $this->client = $this->createClientWithCredentials();
    $this->user = UserFactory::find([
        'username' => UserStory::DEFAULT_USER,
    ]);
});
\it('access forbidden if didn\'t have ROLE_API', function () {
    $page = 1;
    $perPage = 30;

    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/log-auto-login-errors?page='.$page.'&itemsPerPage='.$perPage);

    $this->assertResponseStatusCodeSame(403);
})->group('LogAutoLoginErrorService');

\it('gets successfully response', function () {
    $this->user->addRole('ROLE_API');
    $page = 1;
    $perPage = 30;
    LogAutoLoginErrorStory::load();

    $this->client->request('GET', '/api/log-auto-login-errors?page='.$page.'&itemsPerPage='.$perPage);

    $this->assertResponseIsSuccessful();
})->group('LogAutoLoginErrorService');

\it('gets results with per page defined', function () {
    $this->user->addRole('ROLE_API');
    $page = 1;
    $perPage = 2;

    LogAutoLoginErrorStory::load();

    $response = $this->client->request('GET', '/api/log-auto-login-errors?page='.$page.'&itemsPerPage='.$perPage);

    \expect($response->toArray())->toHaveKey('totalItems');
    \expect($response->toArray())->toHaveKey('member');
    \expect($response->toArray()['member'])->toHaveCount($perPage);
})->group('LogAutoLoginErrorService');

\it('gets results without per page defined', function () {
    $this->user->addRole('ROLE_API');
    $page = 1;
    LogAutoLoginErrorStory::load();

    $response = $this->client->request('GET', '/api/log-auto-login-errors?page='.$page);

    \expect($response->toArray()['member'])->toHaveCount(LogAutoLoginError::DEFAULT_ITEMS_PER_PAGE);
})->group('LogAutoLoginErrorService');
