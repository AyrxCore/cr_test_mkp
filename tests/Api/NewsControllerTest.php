<?php

declare(strict_types=1);

use App\Tests\MockClientCallback;

\afterEach(function () {
    MockClientCallback::reset();
});

\it('returns news list successfully', function () {
    $client = $this::createClientWithCredentials();
    $client->request('GET', '/api/news');

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
        'status' => 'success',
        'count' => 2,
    ]);

    $responseData = \json_decode($client->getResponse()->getContent(), true);

    \expect($responseData['data'])
        ->toBeArray()
        ->toHaveCount(2);

    $firstNews = $responseData['data'][0];
    \expect($firstNews)
        ->toHaveKey('slug', 'test-news-1')
        ->and($firstNews)->toHaveKey('articleTitle')
        ->and($firstNews)->toHaveKey('articleImgMobile')
        ->and($firstNews)->toHaveKey('articleImgDesktop')
        ->and($firstNews['articleImgMobile'])->toHaveKey('filename')
        ->and($firstNews['articleImgDesktop'])->toHaveKey('filename');

    $secondNews = $responseData['data'][1];
    \expect($secondNews)
        ->toHaveKey('slug', 'test-news-2')
        ->and($secondNews)->toHaveKey('bannerImgMobile')
        ->and($secondNews)->toHaveKey('bannerImgDesktop')
        ->and($secondNews['bannerImgMobile'])->toHaveKey('filename')
        ->and($secondNews['bannerImgDesktop'])->toHaveKey('filename');
})->group('news', 'storyblok');

\it('returns empty list when no news available', function () {
    MockClientCallback::setSimulateEmptyNews(true);

    $client = $this::createClientWithCredentials();
    $client->request('GET', '/api/news');

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
        'status' => 'success',
        'count' => 0,
    ]);

    $responseData = \json_decode($client->getResponse()->getContent(), true);
    \expect($responseData['data'])->toBeArray()->toBeEmpty();
})->group('news', 'storyblok');

\it('returns 401 when not authenticated', function () {
    $client = $this::createClient();

    $client->request('GET', '/api/news');

    $this->assertResponseStatusCodeSame(401);
})->group('news', 'storyblok');

\it('returns error when Storyblok API fails', function () {
    MockClientCallback::setSimulateStoryblokError(true);

    $client = $this::createClientWithCredentials();
    $client->request('GET', '/api/news');

    $this->assertResponseStatusCodeSame(500);
    $this->assertJsonContains([
        'error' => 'Failed to fetch news',
    ]);
})->group('news', 'storyblok');
