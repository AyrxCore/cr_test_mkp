<?php

declare(strict_types=1);

\it('gets products', function () {
    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/products?page=1&perPage=5');
    $this->assertJsonResponseMatches('products/response.json');
})->group('products');
