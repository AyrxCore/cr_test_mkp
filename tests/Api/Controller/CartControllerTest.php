<?php

declare(strict_types=1);

use App\Tests\MockClient\DjustMockClientCallback;

\uses()->group('ApiCartController', 'cart');

\afterEach(function () {
    DjustMockClientCallback::reset();
});

\it('returns 401 when not authenticated', function () {
    $client = $this::createClient();

    $client->request('POST', '/api/carts/CART-TEST/logistic-orders/customer-info');

    $this->assertResponseStatusCodeSame(401);
});

\it('returns 400 when cartId does not match the current cart', function () {
    $client = $this::createClientWithCredentials();

    $client->request('POST', '/api/carts/WRONG-CART-ID/logistic-orders/customer-info');

    $this->assertResponseStatusCodeSame(400);
});

\it('returns 200 and patches all logistic orders with customer info', function () {
    DjustMockClientCallback::setSimulateCartWithLogisticOrders(true);

    $client = $this::createClientWithCredentials();

    $client->request('POST', '/api/carts/CART-WITH-LOGISTICS-001/logistic-orders/customer-info');

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);

    $responseData = \json_decode($client->getResponse()->getContent(), true);
    \expect($responseData)->toBeTrue();
});

// --- Tests éco-participation ---

\it('returns 401 when not authenticated on eco-tax endpoint', function () {
    $client = $this::createClient();

    $client->request('POST', '/api/carts/CART-WITH-ECO-TAX-001/logistic-orders/eco-tax');

    $this->assertResponseStatusCodeSame(401);
});

\it('returns 400 when cartId does not match on eco-tax endpoint', function () {
    $client = $this::createClientWithCredentials();

    $client->request('POST', '/api/carts/WRONG-CART-ID/logistic-orders/eco-tax');

    $this->assertResponseStatusCodeSame(400);
});

\it('returns 200 and patches logistic order lines with eco-tax values', function () {
    DjustMockClientCallback::setSimulateCartWithEcoTaxProducts(true);

    $client = $this::createClientWithCredentials();

    $client->request('POST', '/api/carts/CART-WITH-ECO-TAX-001/logistic-orders/eco-tax');

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);

    $responseData = \json_decode($client->getResponse()->getContent(), true);
    \expect($responseData)->toBeTrue();
});

\it('returns 200 when cart has no logistic order lines with eco-tax', function () {
    DjustMockClientCallback::setSimulateCartWithLogisticOrders(true);

    $client = $this::createClientWithCredentials();

    $client->request('POST', '/api/carts/CART-WITH-LOGISTICS-001/logistic-orders/eco-tax');

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);

    $responseData = \json_decode($client->getResponse()->getContent(), true);
    \expect($responseData)->toBeTrue();
});

\it('returns 400 when eco-tax PATCH fails on Djust API', function () {
    DjustMockClientCallback::setSimulateCartWithEcoTaxProducts(true);
    DjustMockClientCallback::setSimulateEcoTaxPatchError(true);

    $client = $this::createClientWithCredentials();

    $client->request('POST', '/api/carts/CART-WITH-ECO-TAX-001/logistic-orders/eco-tax');

    $this->assertResponseStatusCodeSame(400);
});

