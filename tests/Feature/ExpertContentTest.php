<?php

declare(strict_types=1);

\it('gets expert content list', function () {
    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/expert-contents');

    $this->assertResponseStatusCodeSame(200);

    $this->assertJsonResponseMatches('expert-contents/response.json');
})->group('expertContents');

\it('return 404 when trying to get expert content with wrong slug', function () {
    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/expert-contents/wrong-slug');

    $this->assertResponseStatusCodeSame(404);

    $this->assertJsonContains(['hydra:description' => 'Not Found']);
})->group('expertContents');

\it('gets expert content with slug', function () {
    $client = $this::createClientWithCredentials();

    $client->request('GET', '/api/expert-contents/bienvenue');

    $this->assertResponseStatusCodeSame(200);

    $this->assertJsonResponseMatches('expert-contents/get-by-slug.json');
})->group('expertContents');
