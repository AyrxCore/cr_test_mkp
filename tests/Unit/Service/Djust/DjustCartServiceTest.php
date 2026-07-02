<?php

declare(strict_types=1);

use App\Dto\CartItem;
use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustCartItemAction;
use App\Enum\Djust\DjustDefaults;
use App\Service\Djust\DjustCartService;
use App\Service\Djust\DjustHttpClientService;
use App\Service\Djust\DjustStoreViewHeadersBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

uses()->group('UnitDjustCartService', 'cart');

\beforeEach(function () {
    $this->httpClient = Mockery::mock(DjustHttpClientService::class);
    $this->storeViewHeaders = ['dj-store-view' => 'default'];
    $this->storeViewHeadersBuilder = Mockery::mock(DjustStoreViewHeadersBuilder::class);
    $this->storeViewHeadersBuilder->shouldReceive('build')->andReturn($this->storeViewHeaders);
    $this->logger = Mockery::mock(LoggerInterface::class)->shouldIgnoreMissing();
    $this->service = new DjustCartService($this->httpClient, $this->storeViewHeadersBuilder, $this->logger);
});

\afterEach(function () {
    Mockery::close();
});

\it('returns carts array when API call succeeds', function () {
    $apiResponse = [
        'content' => [
            [
                'productCount' => 5,
            ],
        ],
    ];

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value, [
            'isValidated' => 'false',
            'locale' => DjustDefaults::LOCALE->value,
        ], $this->storeViewHeaders)
        ->andReturn($apiResponse);

    $result = $this->service->getCart();

    \expect($result)->toBe($apiResponse['content'][0])
        ->and($result)->toBeArray()
        ->and($result)->toHaveCount(1);
});

\it('creates cart successfully if no cart exists', function () {
    $createdCart = [
        'content' => [
            'productCount' => 0,
        ],
    ];

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value, [
            'isValidated' => 'false',
            'locale' => DjustDefaults::LOCALE->value,
        ], $this->storeViewHeaders)
        ->andReturn([
            'content' => [],
        ]);

    $this->httpClient->shouldReceive('post')
        ->once()
        ->with(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value, [], $this->storeViewHeaders)
        ->andReturn($createdCart);

    $result = $this->service->getCart();

    \expect($result)->toBe($createdCart);
});

\it('returns empty array from API when creating cart', function () {
    $createdCart = [];

    $this->httpClient->shouldReceive('post')
        ->once()
        ->andReturn($createdCart);

    $result = $this->service->createCart();

    \expect($result)->toBeArray()
        ->and($result)->toBeEmpty();
});

\it('updates cart lines successfully', function () {
    $cartId = '0a28ec92-9b22-15e2-819b-2698f98500b8';
    $cartLine1 = new CartItem();
    $cartLine1->setOfferPriceId('OFFER-123');
    $cartLine1->setQuantity(5);
    $cartLine1->setAction(DjustCartItemAction::REPLACE_QUANTITY->value);

    $cartLine2 = new CartItem();
    $cartLine2->setOfferPriceId('OFFER-456');
    $cartLine2->setQuantity(10);
    $cartLine2->setAction(DjustCartItemAction::ADD_QUANTITY->value);

    $cartLines = [$cartLine1, $cartLine2];

    $expectedPayload = [
        'updateOrderCommercialLines' => [
            [
                'updateAction' => DjustCartItemAction::REPLACE_QUANTITY->value,
                'id' => 'OFFER-123',
                'quantity' => 5,
            ],
            [
                'updateAction' => DjustCartItemAction::ADD_QUANTITY->value,
                'id' => 'OFFER-456',
                'quantity' => 10,
            ],
        ],
    ];

    $expectedResponse = [
        'content' => [
            'reference' => $cartId,
            'productCount' => 2,
        ],
    ];

    $expectedEndpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS_ITEMS->value, $cartId);

    $this->httpClient->shouldReceive('put')
        ->once()
        ->with($expectedEndpoint, $expectedPayload)
        ->andReturn($expectedResponse);

    $result = $this->service->updateCartItems($cartId, $cartLines);

    \expect($result)->toBe($expectedResponse)
        ->and($result['content']['productCount'])->toBe(2);
});

\it('updates cart lines with single line', function () {
    $cartId = 'CART-001';
    $cartLine = new CartItem();
    $cartLine->setOfferPriceId('OFFER-789');
    $cartLine->setQuantity(3);
    $cartLine->setAction(DjustCartItemAction::REPLACE_QUANTITY->value);

    $cartLines = [$cartLine];

    $expectedPayload = [
        'updateOrderCommercialLines' => [
            [
                'updateAction' => DjustCartItemAction::REPLACE_QUANTITY->value,
                'id' => 'OFFER-789',
                'quantity' => 3,
            ],
        ],
    ];

    $expectedEndpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS_ITEMS->value, $cartId);

    $expectedResponse = [
        'content' => [
            'reference' => $cartId,
            'productCount' => 1,
        ],
    ];

    $this->httpClient->shouldReceive('put')
        ->once()
        ->with($expectedEndpoint, $expectedPayload)
        ->andReturn($expectedResponse);

    $result = $this->service->updateCartItems($cartId, $cartLines);

    \expect($result)->toBe($expectedResponse)
        ->and($result['content']['productCount'])->toBe(1);
});

\it('updates cart lines with zero quantity', function () {
    $cartId = 'CART-002';
    $cartLine = new CartItem();
    $cartLine->setOfferPriceId('OFFER-100');
    $cartLine->setQuantity(0);
    $cartLine->setAction(DjustCartItemAction::REPLACE_QUANTITY->value);

    $cartLines = [$cartLine];

    $expectedPayload = [
        'updateOrderCommercialLines' => [
            [
                'updateAction' => DjustCartItemAction::REPLACE_QUANTITY->value,
                'id' => 'OFFER-100',
                'quantity' => 0,
            ],
        ],
    ];

    $expectedEndpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS_ITEMS->value, $cartId);

    $expectedResponse = [
        'content' => [
            'reference' => $cartId,
            'productCount' => 0,
        ],
    ];

    $this->httpClient->shouldReceive('put')
        ->once()
        ->with($expectedEndpoint, $expectedPayload)
        ->andReturn($expectedResponse);

    $result = $this->service->updateCartItems($cartId, $cartLines);

    \expect($result['content']['productCount'])->toBe(0);
});

\it('updates cart billing address successfully', function () {
    $cartId = '0a28ec92-9b22-15e2-819b-2698f98500b8';
    $billingAddressId = 'BILLING-ADDRESS-123';

    $expectedEndpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_BILLING_ADDRESS->value, $cartId);
    $expectedPayload = [
        'billingAddressId' => $billingAddressId,
    ];

    $this->httpClient->shouldReceive('put')
        ->once()
        ->with($expectedEndpoint, $expectedPayload);

    $result = $this->service->updateCartBillingAddress($cartId, $billingAddressId);

    \expect($result)->toBeArray();
});

\it('updates cart shipping address successfully', function () {
    $cartId = '0a28ec92-9b22-15e2-819b-2698f98500b8';
    $shippingAddressId = 'SHIPPING-ADDRESS-456';

    $expectedEndpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_SHIPPING_ADDRESS->value, $cartId);
    $expectedPayload = [
        'shippingAddressId' => $shippingAddressId,
        'shippingType' => DjustCartService::SHIPPING_TYPE_STANDARD,
    ];

    $this->httpClient->shouldReceive('put')
        ->once()
        ->with($expectedEndpoint, $expectedPayload);

    $result = $this->service->updateCartShippingAddress($cartId, $shippingAddressId);

    \expect($result)->toBeArray();
});

\it('returns parsed payment methods when API call succeeds', function () {
    $cartId = '0a28ec92-9b22-15e2-819b-2698f98500b8';

    $adyenJson = \json_encode([
        'paymentMethods' => [
            [
                'name' => 'Carte bancaire',
                'type' => 'scheme',
                'brands' => ['maestro', 'amex', 'mc', 'visa'],
            ],
            [
                'name' => 'Virement bancaire international',
                'type' => 'bankTransfer_IBAN',
                'brands' => null,
            ],
        ],
        'storedPaymentMethods' => null,
    ]);

    $apiResponse = [
        'adyenPaymentMethods' => $adyenJson,
        'enableCreditCardStorage' => false,
    ];

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_PAYMENTS_METHODS->value, [
            'reference' => $cartId,
            'countryCode' => DjustDefaults::COUNTRY_CODE->value,
            'locale' => DjustDefaults::LOCALE->value,
        ])
        ->andReturn($apiResponse);

    $result = $this->service->getPaymentMethods($cartId);

    \expect($result)->toBeArray()
        ->and($result)->toHaveKey('paymentMethods')
        ->and($result)->toHaveKey('enableCreditCardStorage')
        ->and($result['enableCreditCardStorage'])->toBeFalse()
        ->and($result['paymentMethods'])->toHaveCount(2)
        ->and($result['paymentMethods'][0]['name'])->toBe('Carte bancaire')
        ->and($result['paymentMethods'][0]['type'])->toBe('scheme')
        ->and($result['paymentMethods'][1]['name'])->toBe('Virement bancaire international');
});

\it('returns empty payment methods array when adyenPaymentMethods is empty JSON', function () {
    $cartId = 'CART-001';

    $apiResponse = [
        'adyenPaymentMethods' => \json_encode(['paymentMethods' => [], 'storedPaymentMethods' => null]),
        'enableCreditCardStorage' => false,
    ];

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_PAYMENTS_METHODS->value, [
            'reference' => $cartId,
            'countryCode' => DjustDefaults::COUNTRY_CODE->value,
            'locale' => DjustDefaults::LOCALE->value,
        ])
        ->andReturn($apiResponse);

    $result = $this->service->getPaymentMethods($cartId);

    \expect($result['paymentMethods'])->toBeArray()
        ->and($result['paymentMethods'])->toBeEmpty();
});

\it('returns empty payment methods array when adyenPaymentMethods key is missing', function () {
    $cartId = 'CART-002';

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_PAYMENTS_METHODS->value, [
            'reference' => $cartId,
            'countryCode' => DjustDefaults::COUNTRY_CODE->value,
            'locale' => DjustDefaults::LOCALE->value,
        ])
        ->andReturn([]);

    $result = $this->service->getPaymentMethods($cartId);

    \expect($result['paymentMethods'])->toBeArray()
        ->and($result['paymentMethods'])->toBeEmpty()
        ->and($result['enableCreditCardStorage'])->toBeFalse();
});

\it('throws BadRequestHttpException when API call fails for payment methods', function () {
    $cartId = 'CART-003';

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_PAYMENTS_METHODS->value, [
            'reference' => $cartId,
            'countryCode' => DjustDefaults::COUNTRY_CODE->value,
            'locale' => DjustDefaults::LOCALE->value,
        ])
        ->andThrow(new \Exception('API unreachable'));

    \expect(fn () => $this->service->getPaymentMethods($cartId))
        ->toThrow(
            BadRequestHttpException::class,
            'Erreur lors de la récupération des moyens de paiement : API unreachable',
        );
});

\it('returns enableCreditCardStorage true when API returns it', function () {
    $cartId = 'CART-004';

    $adyenJson = \json_encode([
        'paymentMethods' => [
            ['name' => 'Carte bancaire', 'type' => 'scheme', 'brands' => ['visa']],
        ],
        'storedPaymentMethods' => null,
    ]);

    $apiResponse = [
        'adyenPaymentMethods' => $adyenJson,
        'enableCreditCardStorage' => true,
    ];

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_PAYMENTS_METHODS->value, [
            'reference' => $cartId,
            'countryCode' => DjustDefaults::COUNTRY_CODE->value,
            'locale' => DjustDefaults::LOCALE->value,
        ])
        ->andReturn($apiResponse);

    $result = $this->service->getPaymentMethods($cartId);

    \expect($result['enableCreditCardStorage'])->toBeTrue();
});

\it('syncCommercialOrder returns response from Djust sync endpoint', function () {
    $cartId = 'CART-SYNC-001';
    $endpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_SYNC->value, $cartId);
    $syncResponse = ['orderLogistics' => []];

    $this->httpClient->shouldReceive('put')
        ->once()
        ->with($endpoint, [])
        ->andReturn($syncResponse);

    $result = $this->service->syncCommercialOrder($cartId);

    \expect($result)->toBe($syncResponse);
});

\it('syncCommercialOrder returns empty array when API call fails', function () {
    $cartId = 'CART-SYNC-FAIL';
    $endpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_SYNC->value, $cartId);

    $this->httpClient->shouldReceive('put')
        ->once()
        ->with($endpoint, [])
        ->andThrow(new \Exception('Network error'));

    $result = $this->service->syncCommercialOrder($cartId);

    \expect($result)->toBeArray()->toBeEmpty();
});

\it('deleteCartLines does nothing when lineIds is empty', function () {
    $this->httpClient->shouldNotReceive('deleteWithBody');

    $this->service->deleteCartLines('CART-001', []);
});

\it('deleteCartLines calls deleteWithBody with correct payload format', function () {
    $cartId = 'CART-DEL-001';
    $lineIds = ['OFFER_AAA', 'OFFER_BBB'];
    $endpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS_ITEMS->value, $cartId);

    $expectedBody = [
        ['offerPriceId' => 'OFFER_AAA'],
        ['offerPriceId' => 'OFFER_BBB'],
    ];

    $this->httpClient->shouldReceive('deleteWithBody')
        ->once()
        ->with($endpoint, $expectedBody)
        ->andReturn([]);

    $this->service->deleteCartLines($cartId, $lineIds);
});

\it('deleteCartLines does not throw when API call fails (idempotent)', function () {
    $cartId = 'CART-DEL-FAIL';
    $lineIds = ['uuid-already-gone'];
    $endpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS_ITEMS->value, $cartId);

    $this->httpClient->shouldReceive('deleteWithBody')
        ->once()
        ->with($endpoint, [['offerPriceId' => 'uuid-already-gone']])
        ->andThrow(new \Exception('404 Not Found'));

    $this->service->deleteCartLines($cartId, $lineIds);
    \expect(true)->toBeTrue();
});

\it('syncAndCleanCart returns empty array when sync response is empty', function () {
    $cartId = 'CART-CLEAN-001';
    $syncEndpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_SYNC->value, $cartId);

    $this->httpClient->shouldReceive('put')
        ->once()
        ->with($syncEndpoint, [])
        ->andReturn([]);

    $this->httpClient->shouldNotReceive('deleteWithBody');

    $result = $this->service->syncAndCleanCart($cartId);

    \expect($result)->toBeArray()->toBeEmpty();
});

\it('syncAndCleanCart detects single blocking line in list', function () {
    $cartId = 'CART-CLEAN-B';
    $syncEndpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_SYNC->value, $cartId);
    $deleteEndpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS_ITEMS->value, $cartId);
    $offerId = 'OFFER_VAE0001P_VAE0001P-pastel-M_CLASS';

    $this->httpClient->shouldReceive('put')
        ->once()
        ->with($syncEndpoint, [])
        ->andReturn([
            [
                'id'      => $offerId,
                'code'    => 'F_W_014',
                'detail'  => 'The product associated with the offer price is inactive.',
                'blocked' => true,
            ],
        ]);

    $this->httpClient->shouldReceive('deleteWithBody')
        ->once()
        ->with($deleteEndpoint, [['offerPriceId' => $offerId]])
        ->andReturn([]);

    $result = $this->service->syncAndCleanCart($cartId);

    \expect($result)->toBe([$offerId]);
});

\it('syncAndCleanCart detects blocking lines in root-level list (Format A)', function () {
    $cartId = 'CART-CLEAN-A';
    $syncEndpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_SYNC->value, $cartId);
    $deleteEndpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS_ITEMS->value, $cartId);
    $offerId1 = 'OFFER_AAA';
    $offerId2 = 'OFFER_BBB';

    $this->httpClient->shouldReceive('put')
        ->once()
        ->with($syncEndpoint, [])
        ->andReturn([
            ['id' => $offerId1, 'code' => 'F_W_014', 'detail' => 'Inactive.', 'blocked' => true],
            ['id' => $offerId2, 'code' => 'F_W_014', 'detail' => 'Inactive.', 'blocked' => true],
        ]);

    $this->httpClient->shouldReceive('deleteWithBody')
        ->once()
        ->with($deleteEndpoint, [['offerPriceId' => $offerId1], ['offerPriceId' => $offerId2]])
        ->andReturn([]);

    $result = $this->service->syncAndCleanCart($cartId);

    \expect($result)->toBe([$offerId1, $offerId2]);
});

\it('syncAndCleanCart ignores non-blocked items in list', function () {
    $cartId = 'CART-CLEAN-SKIP';
    $syncEndpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_SYNC->value, $cartId);

    $this->httpClient->shouldReceive('put')
        ->once()
        ->with($syncEndpoint, [])
        ->andReturn([
            ['id' => 'OFFER_OK', 'code' => 'F_W_001', 'detail' => 'Warning only.', 'blocked' => false],
        ]);

    $this->httpClient->shouldNotReceive('deleteWithBody');

    $result = $this->service->syncAndCleanCart($cartId);

    \expect($result)->toBeEmpty();
});

\it('syncAndCleanCart retourne les ids bloquants avec doublons si l\'API en renvoie', function () {
    $cartId = 'CART-CLEAN-DUP';
    $syncEndpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_SYNC->value, $cartId);
    $deleteEndpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS_ITEMS->value, $cartId);
    $offerId = 'OFFER_DUP_333';

    $this->httpClient->shouldReceive('put')
        ->once()
        ->with($syncEndpoint, [])
        ->andReturn([
            ['id' => $offerId, 'code' => 'F_W_014', 'blocked' => true],
            ['id' => $offerId, 'code' => 'F_W_014', 'blocked' => true],
        ]);

    $this->httpClient->shouldReceive('deleteWithBody')
        ->once()
        ->with($deleteEndpoint, [['offerPriceId' => $offerId], ['offerPriceId' => $offerId]])
        ->andReturn([]);

    $result = $this->service->syncAndCleanCart($cartId);

    \expect($result)->toBe([$offerId, $offerId]);
});

\it('updateLogisticOrderCustomFields appelle le bon endpoint avec les custom fields', function () {
    $logisticOrderId = 'ORDER-LOGISTIC-123';
    $siret = '12345678900000';
    $email = 'test@example.com';
    $phone = '0601020304';

    $endpoint = \sprintf(\App\Enum\Djust\DjustApiEndpoint::SHOP_LOGISTIC_ORDER->value, $logisticOrderId);

    $expectedPayload = [
        'customFieldValues' => [
            ['customFieldId' => 'SIRET_CLIENT', 'customFieldValue' => $siret],
            ['customFieldId' => 'EMAIL_ADDRESS', 'customFieldValue' => $email],
            ['customFieldId' => 'TEL_NUMBER', 'customFieldValue' => $phone],
        ],
    ];

    $this->httpClient->shouldReceive('patch')
        ->once()
        ->with($endpoint, $expectedPayload, $this->storeViewHeaders)
        ->andReturn([]);

    $this->service->updateLogisticOrderCustomFields($logisticOrderId, $siret, $email, $phone);
})->group('UnitDjustCartService', 'cart');

\it('updateLogisticOrderCustomFields remplace les valeurs nulles par des chaînes vides', function () {
    $logisticOrderId = 'ORDER-LOGISTIC-456';

    $endpoint = \sprintf(\App\Enum\Djust\DjustApiEndpoint::SHOP_LOGISTIC_ORDER->value, $logisticOrderId);

    $this->httpClient->shouldReceive('patch')
        ->once()
        ->withArgs(function (string $ep, array $payload, array $headers) use ($endpoint): bool {
            if ($ep !== $endpoint) {
                return false;
            }
            foreach ($payload['customFieldValues'] as $cf) {
                if ($cf['customFieldValue'] !== '') {
                    return false;
                }
            }
            return isset($headers['dj-store-view']);
        })
        ->andReturn([]);

    $this->service->updateLogisticOrderCustomFields($logisticOrderId, null, null, null);
})->group('UnitDjustCartService', 'cart');

\it('updateLogisticOrderCustomFields ne lève pas d\'exception si l\'API échoue', function () {
    $logisticOrderId = 'ORDER-LOGISTIC-FAIL';

    $endpoint = \sprintf(\App\Enum\Djust\DjustApiEndpoint::SHOP_LOGISTIC_ORDER->value, $logisticOrderId);

    $this->httpClient->shouldReceive('patch')
        ->once()
        ->with($endpoint, \Mockery::any(), $this->storeViewHeaders)
        ->andThrow(new \RuntimeException('API Error'));

    // Ne doit pas lever d'exception
    $this->service->updateLogisticOrderCustomFields($logisticOrderId, 'siret', 'email@test.com', '0600000000');
})->group('UnitDjustCartService', 'cart');

\it('updateAllLogisticOrdersCustomFields appelle patch pour chaque logistic order', function () {
    $djustCart = [
        'orderLogistics' => [
            ['reference' => 'LOGISTIC-001'],
            ['reference' => 'LOGISTIC-002'],
        ],
    ];

    $endpoint1 = \sprintf(\App\Enum\Djust\DjustApiEndpoint::SHOP_LOGISTIC_ORDER->value, 'LOGISTIC-001');
    $endpoint2 = \sprintf(\App\Enum\Djust\DjustApiEndpoint::SHOP_LOGISTIC_ORDER->value, 'LOGISTIC-002');

    $this->httpClient->shouldReceive('patch')
        ->once()
        ->with($endpoint1, \Mockery::any(), $this->storeViewHeaders)
        ->andReturn([]);

    $this->httpClient->shouldReceive('patch')
        ->once()
        ->with($endpoint2, \Mockery::any(), $this->storeViewHeaders)
        ->andReturn([]);

    $this->service->updateAllLogisticOrdersCustomFields($djustCart, '53849238000026', 'test@example.com', '0601020304');
})->group('UnitDjustCartService', 'cart');

\it('updateAllLogisticOrdersCustomFields ignore les logistic orders sans reference', function () {
    $djustCart = [
        'orderLogistics' => [
            ['id' => 'only-an-id-no-reference'],
            [],
        ],
    ];

    $this->httpClient->shouldNotReceive('patch');

    $this->service->updateAllLogisticOrdersCustomFields($djustCart, 'siret', 'email@test.com', '0600000000');
})->group('UnitDjustCartService', 'cart');

\it('updateAllLogisticOrdersCustomFields ne fait rien si orderLogistics est absent', function () {
    $this->httpClient->shouldNotReceive('patch');

    $this->service->updateAllLogisticOrdersCustomFields([], 'siret', 'email@test.com', '0600000000');
})->group('UnitDjustCartService', 'cart');

// --- isSuccessfulPaymentResult ---

\it('isSuccessfulPaymentResult returns true for authorised resultCode', function () {
    \expect($this->service->isSuccessfulPaymentResult(['resultCode' => 'Authorised']))->toBeTrue();
});

\it('isSuccessfulPaymentResult returns true for pending resultCode', function () {
    \expect($this->service->isSuccessfulPaymentResult(['resultCode' => 'Pending']))->toBeTrue();
});

\it('isSuccessfulPaymentResult returns false for refused resultCode', function () {
    \expect($this->service->isSuccessfulPaymentResult(['resultCode' => 'Refused']))->toBeFalse();
});

\it('isSuccessfulPaymentResult returns false for cancelled resultCode', function () {
    \expect($this->service->isSuccessfulPaymentResult(['resultCode' => 'Cancelled']))->toBeFalse();
});

\it('isSuccessfulPaymentResult returns false for error resultCode', function () {
    \expect($this->service->isSuccessfulPaymentResult(['resultCode' => 'Error']))->toBeFalse();
});

\it('isSuccessfulPaymentResult returns false when resultCode is missing', function () {
    \expect($this->service->isSuccessfulPaymentResult([]))->toBeFalse();
});

// --- isPaymentImmediatelyAuthorised ---

\it('isPaymentImmediatelyAuthorised returns true when no action (direct payment)', function () {
    \expect($this->service->isPaymentImmediatelyAuthorised(['resultCode' => 'Authorised']))->toBeTrue();
});

\it('isPaymentImmediatelyAuthorised returns true for bankTransfer action', function () {
    \expect($this->service->isPaymentImmediatelyAuthorised([
        'resultCode' => 'Authorised',
        'action' => ['type' => 'bankTransfer'],
    ]))->toBeTrue();
});

\it('isPaymentImmediatelyAuthorised returns false for redirect action (3DS)', function () {
    \expect($this->service->isPaymentImmediatelyAuthorised([
        'resultCode' => 'RedirectShopper',
        'action' => ['type' => 'redirect'],
    ]))->toBeFalse();
});

\it('isPaymentImmediatelyAuthorised returns false when payment failed', function () {
    \expect($this->service->isPaymentImmediatelyAuthorised(['resultCode' => 'Refused']))->toBeFalse();
});

