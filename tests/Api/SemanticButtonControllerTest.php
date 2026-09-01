<?php

declare(strict_types=1);

use App\Tests\MockClientCallback;

\afterEach(function () {
    MockClientCallback::reset();
});

\it('returns semantic buttons list successfully for the current channel', function () {
    $client = $this::createClientWithCredentials();
    $client->request('GET', '/api/semantic_buttons');

    $this->assertResponseStatusCodeSame(200);

    $responseData = \json_decode($client->getResponse()->getContent(), true);

    \expect($responseData)->toBeArray()->toHaveCount(3);

    $sectionTitleItem = $responseData[0];
    \expect($sectionTitleItem)
        ->toHaveKey('id', 0)
        ->and($sectionTitleItem)->toHaveKey('sectionTitle', 'Nos univers')
        ->and($sectionTitleItem)->not->toHaveKey('label')
        ->and($sectionTitleItem)->not->toHaveKey('search');

    $firstButton = $responseData[1];
    \expect($firstButton)
        ->toHaveKey('id', 1)
        ->and($firstButton)->toHaveKey('label', 'Bureautique')
        ->and($firstButton)->toHaveKey('search', 'bureautique')
        ->and($firstButton)->not->toHaveKey('sectionTitle');

    $secondButton = $responseData[2];
    \expect($secondButton)
        ->toHaveKey('id', 2)
        ->and($secondButton)->toHaveKey('label', 'Mobilier')
        ->and($secondButton)->toHaveKey('search', 'mobilier');
})->group('semantic_button', 'storyblok');

\it('returns an empty list when no semantic buttons story matches the channel', function () {
    MockClientCallback::setSimulateEmptySemanticButtons(true);

    $client = $this::createClientWithCredentials();
    $client->request('GET', '/api/semantic_buttons');

    $this->assertResponseStatusCodeSame(200);

    $responseData = \json_decode($client->getResponse()->getContent(), true);
    \expect($responseData)->toBeArray()->toBeEmpty();
})->group('semantic_button', 'storyblok');

\it('returns 401 when not authenticated', function () {
    $client = $this::createClient();

    $client->request('GET', '/api/semantic_buttons');

    $this->assertResponseStatusCodeSame(401);
})->group('semantic_button', 'storyblok');

\it('returns error when Storyblok API fails', function () {
    MockClientCallback::setSimulateStoryblokError(true);

    $client = $this::createClientWithCredentials();
    $client->request('GET', '/api/semantic_buttons');

    $this->assertResponseStatusCodeSame(500);
    $this->assertJsonContains([
        'error' => 'Failed to fetch semantic buttons',
    ]);
})->group('semantic_button', 'storyblok');
