<?php

declare(strict_types=1);

use App\DataFixtures\Story\UserStory;

\it('gets empty saved carts list', function () {
    UserStory::load();

    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/saved-carts');
    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonResponseMatches('cart/empty-saved-carts-list-response.json');
})->group('cart');

\it('saves a cart', function () {
    UserStory::load();

    $client = $this::createClientWithCredentials();

    $client->request('POST', '/api/saved-carts', [
        'json' => [
            'name' => 'test cart',
        ],
    ]);
    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(201);
    $this->assertJsonResponseMatches('cart/create-saved-cart-response.json');

    $client->request('GET', '/api/saved-carts');
    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonResponseMatches('cart/get-saved-cart-response.json');
})->group('cart');
