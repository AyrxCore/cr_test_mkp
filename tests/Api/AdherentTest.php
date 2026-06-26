<?php

declare(strict_types=1);

use App\DataFixtures\Factory\UserFactory;
use App\Tests\Story\Account\UserStory;

\it('returns 404 error when trying to get a non-existing adherent', function () {
    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/adherents/1ee3048a-6285-6604-8d8d-2b8466b1f427');

    $this->assertResponseStatusCodeSame(404);
    $this->assertJsonContains(['description' => 'Adherent not found']);
})->group('adherents');

\it('gets an adherent on a existing channel', function () {
    $client = $this::createClientWithCredentials();

    $user = UserFactory::find(['username' => UserStory::DEFAULT_USER]);
    $accounts = $user->getAccounts();
    $adherentId = $accounts->first()->getAdherent()->getId();

    $client->request('GET', \sprintf('/api/adherents/%s', $adherentId));

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonResponseMatches('adherent/get-by-id-success-response.json');
})->group('adherents');
