<?php

declare(strict_types=1);

use App\DataFixtures\Factory\AdherentTarifShowcaseFactory;
use App\DataFixtures\Factory\UserFactory;
use App\Tests\Story\Account\UserStory;

\it('returns 404 when trying to request contact for a non-existing showcase', function () {
    $client = $this::createClientWithCredentials();

    $client->request('PATCH', '/api/adherent_tarif_showcases/999999/request-contact', [
        'headers' => ['Content-Type' => 'application/merge-patch+json'],
        'body' => json_encode(['accordName' => 'Test Accord']),
    ]);

    $this->assertResponseStatusCodeSame(404);
})->group('adherent-tarif-showcase');

\it('returns 403 when trying to request contact for a showcase from another adherent', function () {
    $client = $this::createClientWithCredentials();

    // Create a showcase for another adherent
    $showcase = AdherentTarifShowcaseFactory::createOne([
        'contactRequested' => false,
    ]);

    $client->request('PATCH', "/api/adherent_tarif_showcases/{$showcase->getId()}/request-contact", [
        'headers' => ['Content-Type' => 'application/merge-patch+json'],
        'body' => json_encode(['accordName' => 'Test Accord']),
    ]);

    $this->assertResponseStatusCodeSame(403);
})->group('adherent-tarif-showcase');

\it('successfully requests contact for a showcase', function () {
    $client = $this::createClientWithCredentials();

    $user = UserFactory::find(['username' => UserStory::DEFAULT_USER]);
    $account = $this::getUserFirstAccount($user);
    $adherent = $account->getAdherent();

    $showcase = AdherentTarifShowcaseFactory::createOne([
        'adherent' => $adherent,
        'contactRequested' => false,
    ]);

    $client->request('PATCH', "/api/adherent_tarif_showcases/{$showcase->getId()}/request-contact", [
        'headers' => ['Content-Type' => 'application/merge-patch+json'],
        'body' => json_encode(['accordName' => 'Test Accord']),
    ]);

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonResponseMatches('adherent-tarif-showcase/request-contact-success-response.json');
})->group('adherent-tarif-showcase');

\it('returns existing showcase when contact already requested', function () {
    $client = $this::createClientWithCredentials();

    $user = UserFactory::find(['username' => UserStory::DEFAULT_USER]);
    $account = $this::getUserFirstAccount($user);
    $adherent = $account->getAdherent();

    $showcase = AdherentTarifShowcaseFactory::createOne([
        'adherent' => $adherent,
        'contactRequested' => true,
    ]);

    $client->request('PATCH', "/api/adherent_tarif_showcases/{$showcase->getId()}/request-contact", [
        'headers' => ['Content-Type' => 'application/merge-patch+json'],
        'body' => json_encode(['accordName' => 'Test Accord']),
    ]);

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonResponseMatches('adherent-tarif-showcase/request-contact-already-requested-response.json');
})->group('adherent-tarif-showcase');
