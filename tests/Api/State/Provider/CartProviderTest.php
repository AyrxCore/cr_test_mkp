<?php

declare(strict_types=1);

\it('gets cart successfully if cart already exist', function () {
    $client = $this->createClientWithCredentials();
    $client->request('GET', '/api/cart');

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
})->group('ApiCartProviderTest', 'cart');
