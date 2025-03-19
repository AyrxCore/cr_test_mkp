<?php

declare(strict_types=1);

\it('gets sellers', function () {
    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/sellers');
    $this->assertJsonResponseMatches('sellers/response.json');
})->group('sellers');
