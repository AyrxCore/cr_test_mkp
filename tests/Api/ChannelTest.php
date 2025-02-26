<?php

declare(strict_types=1);

use App\DataFixtures\Factory\ChannelFactory;
use App\Tests\Story\Channel\ChannelParameterStory;
use App\Tests\Story\Channel\ChannelStory;

\it('returns 404 on collection operations', function (
    string $method,
    string $expectedResponse
) {
    $client = $this::createClientWithCredentials();

    $client->request($method, '/api/channels/');

    $this->assertResponseStatusCodeSame(404);
    $this->assertJsonResponseMatches($expectedResponse);
})
    ->with([
        'GET' => [
            'method' => 'GET',
            'expectedResponse' => 'channel/get-collection-not-found-response.json',
        ],
        'POST' => [
            'method' => 'POST',
            'expectedResponse' => 'channel/post-not-found-response.json',
        ],
    ])
    ->group('channels');

\it('returns a 405 Method Not Allowed when trying to PATCH a channel', function () {
    $client = $this::createClientWithCredentials();

    $client->request('PATCH', '/api/channels/8e211f4d-c2c9-4fdb-9031-5414d019b488');

    $this->assertResponseStatusCodeSame(405);
    $this->assertJsonResponseMatches('channel/patch-not-found-response.json');
})
    ->group('channels');

\it('returns a 405 Method Not Allowed when trying to DELETE a channel', function () {
    ChannelStory::load();

    $channel = ChannelFactory::first();

    $client = $this::createClientWithCredentials();

    $client->request('DELETE', \sprintf('/api/channels/%s', $channel->getId()));

    $this->assertResponseStatusCodeSame(405);
    $this->assertJsonResponseMatches('channel/delete-not-allowed-response.json');
})->group('channels');

\it('returns a 404 when trying to GET a channel by ID', function () {
    ChannelStory::load();

    $channel = ChannelFactory::first();

    $client = $this::createClientWithCredentials();

    $client->request('GET', \sprintf('/api/channels/%s', $channel->getId()));

    $this->assertResponseStatusCodeSame(404);
    $this->assertJsonContains(['hydra:description' => '']);
})->group('channels');

\it('returns a 404 when trying to get a non-existing channel by host', function () {
    $client = $this::createClient();

    $client->request('GET', '/api/channels/by-host/some.unknown-host.com', [
        'headers' => ['Accept' => 'application/json'],
    ]);

    $this->assertResponseStatusCodeSame(404);
})->group('channels');

\it('gets a channel by host', function () {
    ChannelStory::load();
    ChannelParameterStory::load();

    $client = $this::createClient();

    $client->request('GET', '/api/channels/by-host/test.qantis.local', [
        'headers' => ['Accept' => 'application/json'],
    ]);

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonResponseMatches('channel/get-by-host-success-response.json');
})->group('channels');
