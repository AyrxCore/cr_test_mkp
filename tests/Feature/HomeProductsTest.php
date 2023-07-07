<?php

declare(strict_types=1);

\it('gets home products', function () {
    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/home-products');

    $this->assertJsonResponseMatches('home-products/response.json');
})->group('home');
