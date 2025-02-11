<?php

declare(strict_types=1);

use App\DataFixtures\Factory\UserFactory;

\it('returns 404 error when trying to get a non-existing adherent', function () {
    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/adherents/1ee3048a-6285-6604-8d8d-2b8466b1f427');

    $this->assertResponseStatusCodeSame(404);
    $this->assertJsonContains(['hydra:description' => 'Adherent not found']);
})->group('adherents');

\it('returns 400 error when trying to get an adherent on a non-existing channel', function () {
    $badChannel = 'BAD-CHANNEL';
    $client = $this::createClientWithCredentials(channel: $badChannel);

    $user = UserFactory::find(['username' => $this::DEFAULT_USER_LOGIN]);
    $accounts = $user->getAccounts();
    $adherentId = $accounts->first()->getAdherent()->getId();

    $client->request('GET', \sprintf('/api/adherents/%s', $adherentId));

    $this->assertResponseStatusCodeSame(400);
    $this->assertJsonContains(['hydra:description' => \sprintf('No such channel "%s".', $badChannel)]);
})->group('adherents');

\it('returns 403 error when trying to get an adherent on a channel where he has no access', function () {
    $client = $this::createClientWithCredentials();

    $user = UserFactory::find(['username' => $this::DEFAULT_USER_LOGIN]);
    $accounts = $user->getAccounts();
    $adherentId = $accounts->first()->getAdherent()->getId();

    $client->request('GET', \sprintf('/api/adherents/%s', $adherentId));

    $this->assertResponseStatusCodeSame(403);
    $this->assertJsonContains(['hydra:description' => 'Access to channel is forbidden']);
})->group('adherents');

\it('gets an adherent on a existing channel', function () {
    $client = $this::createClientWithCredentials(channel: 'QANTIS_ACHAT');

    $user = UserFactory::find(['username' => $this::DEFAULT_USER_LOGIN]);
    $accounts = $user->getAccounts();
    $adherentId = $accounts->first()->getAdherent()->getId();

    $client->request('GET', \sprintf('/api/adherents/%s', $adherentId));

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonResponseMatches('adherent/get-by-id-success-response.json');
})->group('adherents');
