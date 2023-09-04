<?php

declare(strict_types=1);

\it('gets banner', function () {
    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/banners/1');

    $this->assertResponseStatusCodeSame(200);

    $this->assertJsonResponseMatches('banner/response.json');
})->group('banner');
