<?php

declare(strict_types=1);

\uses()->group('ApiProductProvider');

\it('gets a product by id from Djust API', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products/cahier_ext');

    $this->assertResponseIsSuccessful();
    $this->assertJsonContains(['@type' => 'Product']);

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('name');
    \expect($response)->toHaveKey('price');
    \expect($response)->toHaveKey('images');
    \expect($response)->toHaveKey('seller');
    \expect($response)->toHaveKey('externalId');
    \expect($response['externalId'])->toBe('cahier_ext');
});

\it('gets a product with seller information', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products/cahier_ext');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('seller');
    \expect($response['seller'])->toHaveKey('name');
    \expect($response['seller'])->toHaveKey('id');
});

\it('gets a product with properties', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products/cahier_ext');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('properties');
    \expect($response['properties'])->toBeArray();
});

\it('gets a product with attachments', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products/cahier_ext');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('attachments');
    \expect($response['attachments'])->toBeArray();
});

\it('gets a product with quantity limits', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products/cahier_ext');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('minOrderQuantity');
    \expect($response)->toHaveKey('maxOrderQuantity');
});

\it('gets a product with categories', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products/cahier_ext');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('categories');
    \expect($response['categories'])->toBeArray();
});

\it('returns 404 when product not found', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products/UNKNOWN_ID');

    $this->assertResponseStatusCodeSame(404);
});

\it('gets a product with productType', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products/cahier_ext');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('productType');
    \expect($response['productType'])->toBeIn(['SELLABLE', 'NOT_SELLABLE', 'ACCORD_CADRE']);
});

\it('gets NOT_SELLABLE product with custom fields', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products/cahier_ext');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();

    // Si le produit est NOT_SELLABLE, il peut avoir ces champs
    if ($response['productType'] === 'NOT_SELLABLE') {
        // Ces champs sont optionnels
        if (isset($response['productTopLabel'])) {
            \expect($response['productTopLabel'])->toBeString();
        }
        if (isset($response['productPricingPhrase'])) {
            \expect($response['productPricingPhrase'])->toBeString();
        }
    }
});

\it('gets product collection from search', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products', ['headers' => ['Accept' => 'application/json']]);

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toBeArray();
    \expect($response)->toHaveKey('results');
    \expect($response)->toHaveKey('resultsCount');
    \expect($response)->toHaveKey('page');
    \expect($response)->toHaveKey('filters');
});

\it('gets product collection with pagination info', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products', ['headers' => ['Accept' => 'application/json']]);

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('results');
    \expect($response)->toHaveKey('resultsCount');
    \expect($response)->toHaveKey('page');
    \expect($response['resultsCount'])->toBeInt();
    \expect($response['page'])->toBeInt();
});

\it('filters product collection by name', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products?name=test', ['headers' => ['Accept' => 'application/json']]);

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('results');
    \expect($response['results'])->toBeArray();
});

\it('handles empty product collection', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products?name=NONEXISTENT_PRODUCT_NAME_THAT_SHOULD_NOT_EXIST', ['headers' => ['Accept' => 'application/json']]);

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('results');
    \expect($response['results'])->toBeArray();
    \expect($response['resultsCount'])->toBeGreaterThanOrEqual(0);
});

\it('supports locale filter in collection', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products?locale=fr-FR', ['headers' => ['Accept' => 'application/json']]);

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('results');
    \expect($response['results'])->toBeArray();
});

\it('returns different pages of products', function () {
    $client = $this->createClientWithCredentials();

    // First page
    $client->request('GET', '/api/products?page=0&perPage=5', ['headers' => ['Accept' => 'application/json']]);
    $this->assertResponseIsSuccessful();
    $page1Response = $client->getResponse()->toArray();

    // Second page
    $client->request('GET', '/api/products?page=1&perPage=5', ['headers' => ['Accept' => 'application/json']]);
    $this->assertResponseIsSuccessful();
    $page2Response = $client->getResponse()->toArray();

    // Check both responses are valid
    \expect($page1Response)->toHaveKey('results');
    \expect($page2Response)->toHaveKey('results');

    // If there are enough products, pages should be different
    if ($page1Response['resultsCount'] > 5) {
        $firstProductPage1 = $page1Response['results'][0]['id'] ?? null;
        $firstProductPage2 = $page2Response['results'][0]['id'] ?? null;

        if ($firstProductPage1 && $firstProductPage2) {
            \expect($firstProductPage1)->not()->toBe($firstProductPage2);
        }
    }
});

\it('returns collection with proper JSON structure', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products', ['headers' => ['Accept' => 'application/json']]);

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('results');
    \expect($response)->toHaveKey('resultsCount');
    \expect($response)->toHaveKey('page');
    \expect($response)->toHaveKey('filters');
    \expect($response['results'])->toBeArray();
    \expect($response['filters'])->toBeArray();
});

\it('returns split-search response with accordCadres and accordCadresCount when splitSearch=1', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/products?splitSearch=1', ['headers' => ['Accept' => 'application/json']]);

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('results');
    \expect($response)->toHaveKey('resultsCount');
    \expect($response)->toHaveKey('accordCadres');
    \expect($response)->toHaveKey('accordCadresCount');
    \expect($response['accordCadres'])->toBeArray();
    \expect($response['accordCadresCount'])->toBeInt();
    \expect($response['accordCadresCount'])->toBe(\count($response['accordCadres']));
});
