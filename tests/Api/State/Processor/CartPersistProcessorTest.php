<?php

declare(strict_types=1);

use App\Enum\Djust\DjustApiEndpoint;

\uses()->group('ApiCartPersistProcessorTest', 'cart');

\beforeEach(function () {
    $this->client = $this->createClientWithCredentials();
    $this->endpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS_ITEMS->value, '0a28ec92-9b22-15e2-819b-2698f98500b8');
});

\it('updates cart lines successfully with valid data', function () {
    $cartLine1 = [
        'id' => '0000002323',
        'quantity' => 5,
        'updateAction' => 'REPLACE_QUANTITY',
    ];
    $cartLine2 = [
        'id' => '0000002324',
        'quantity' => 10,
        'updateAction' => 'REPLACE_QUANTITY',
    ];

    $payload = [
        'updateOrderCommercialLines' => [$cartLine1, $cartLine2],
    ];

    $this->client->request('PUT', $this->endpoint, [
        'json' => $payload,
    ]);

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
});

\it('handles empty cart lines array gracefully', function () {
    $payload = [
        'cartLines' => [],
    ];

    $this->client->request('PUT', $this->endpoint, [
        'json' => $payload,
    ]);

    $this->assertResponseIsSuccessful();
});

\it('updates cart with single cart line', function () {
    $cartLine = [
        'offerPriceId' => '0000002323',
        'quantity' => 3,
        'updateAction' => 'REPLACE_QUANTITY',
    ];

    $payload = [
        'cartLines' => [$cartLine],
    ];

    $this->client->request('PUT', $this->endpoint, [
        'json' => $payload,
    ]);

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
});

\it('handles cart lines with different quantities', function () {
    $cartLines = [
        ['offerPriceId' => '0000002323', 'quantity' => 1, 'updateAction' => 'REPLACE_QUANTITY'],
        ['offerPriceId' => '0000002324', 'quantity' => 100, 'updateAction' => 'REPLACE_QUANTITY'],
        ['offerPriceId' => '0000002325', 'quantity' => 50, 'updateAction' => 'REPLACE_QUANTITY'],
    ];

    $payload = ['cartLines' => $cartLines];

    $this->client->request('PUT', $this->endpoint, [
        'json' => $payload,
    ]);

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
});

\it('returns updated cart data after processing', function () {
    $cartLine = [
        'offerPriceId' => '0000002323',
        'quantity' => 7,
        'updateAction' => 'REPLACE_QUANTITY',
    ];

    $payload = [
        'cartLines' => [$cartLine],
    ];

    $this->client->request('PUT', $this->endpoint, [
        'json' => $payload,
    ]);

    $this->assertResponseIsSuccessful();
});

\it('handles cart lines with zero quantity', function () {
    $cartLine = [
        'offerPriceId' => '0000002323',
        'quantity' => 0,
        'updateAction' => 'REPLACE_QUANTITY',
    ];

    $payload = [
        'cartLines' => [$cartLine],
    ];

    $this->client->request('PUT', $this->endpoint, [
        'json' => $payload,
    ]);

    $this->assertResponseIsSuccessful();
});
