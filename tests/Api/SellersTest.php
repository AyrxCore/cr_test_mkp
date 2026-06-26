<?php

declare(strict_types=1);

use App\Tests\MockClientCallback;

\uses()->group('ApiSellers');

\afterEach(function () {
    MockClientCallback::reset();
});

\it('gets sellers', function () {
    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/sellers', ['headers' => ['Accept' => 'application/json']]);
    $this->assertJsonResponseMatches('sellers/response.json');
});

\it('returns empty sellers list when no accord cadre story exists', function () {
    MockClientCallback::setSimulateEmptyAccordCadre(true);
    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/sellers', ['headers' => ['Accept' => 'application/json']]);

    $this->assertResponseStatusCodeSame(200);
    \expect(\json_decode($client->getResponse()->getContent(), true))->toBeArray()->toBeEmpty();
});
