<?php

declare(strict_types=1);

use App\DataFixtures\Factory\AccordFactory;
use App\DataFixtures\Factory\PartnerFactory;
use App\DataFixtures\Factory\PartnerStoreFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

\beforeEach(function () {
    $this->client = $this->createClientWithCredentials();
});

\it('throws not found accord by id', function () {
    $accordId = Uuid::v4();

    $response = $this->client->request('GET', '/api/accords/'.$accordId);

    $this->assertEquals(404, $response->getStatusCode());
})->group('AccordView');

\it('gets only linked accord stores by accord by id', function () {
    $partner = PartnerFactory::createOne();
    PartnerStoreFactory::createOne([
        'partner' => $partner,
    ]);

    PartnerStoreFactory::createMany(4, [
        'partner' => $partner,
    ]);

    $accordStores = PartnerStoreFactory::createMany(3, [
        'partner' => $partner,
    ]);
    $accord = AccordFactory::createOne([
        'hasStore' => true,
        'partner' => $partner,
        'stores' => $accordStores,
    ]);

    $response = $this->client->request('GET', '/api/accords/'.$accord->getId());
    $this->assertEquals(200, $response->getStatusCode());

    $data = \json_decode($response->getContent(false), true);
    \expect($data)->toHaveKeys(['id', 'logo', 'name', 'stores']);
    \expect($data)->not->toHaveKeys(['partner', 'createdAt', 'updatedAt']);
    \expect($data['stores'])->toHaveCount(3);
    \expect($data['name'])->toBe($accord->getName());
    \expect($data['logo'])->toBe($accord->getLogo());
    $accordStore = $data['stores'][0];
    \expect($accordStore)->toHaveKeys(['name', 'address', 'latitude', 'longitude']);
})->group('AccordView');

\it('gets all partner stores by accord by id', function () {
    $partner = PartnerFactory::createOne();
    $accord = AccordFactory::createOne([
        'hasStore' => true,
        'partner' => $partner,
    ]);

    PartnerStoreFactory::createMany(5, [
        'partner' => $partner,
    ]);

    $response = $this->client->request('GET', '/api/accords/'.$accord->getId());
    $this->assertEquals(200, $response->getStatusCode());

    $data = \json_decode($response->getContent(false), true);
    \expect($data)->toHaveKeys(['id', 'logo', 'name', 'stores']);
    \expect($data)->not->toHaveKeys(['partner', 'createdAt', 'updatedAt']);
    \expect($data['stores'])->toHaveCount(5);
})->group('AccordView');

\it('return empty stores array if accord has_store property is false', function () {
    $partner = PartnerFactory::createOne();
    $accord = AccordFactory::createOne([
        'hasStore' => false,
        'stores' => new ArrayCollection([PartnerStoreFactory::createOne([
            'partner' => $partner,
        ])]),
    ]);

    $response = $this->client->request('GET', '/api/accords/'.$accord->getId());
    $this->assertEquals(200, $response->getStatusCode());

    $data = \json_decode($response->getContent(false), true);
    \expect($data)->toHaveKeys(['id', 'logo', 'name', 'stores']);
    \expect($data)->not->toHaveKeys(['partner', 'createdAt', 'updatedAt']);
    \expect($data['stores'])->toHaveCount(0);
})->group('AccordView');
