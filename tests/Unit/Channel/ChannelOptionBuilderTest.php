<?php

declare(strict_types=1);

use App\Service\Channel\ChannelOptionsBuilder;

\beforeEach(function () {
    $validKeys = ['key1', 'key2', 'key3'];
    $this->builder = new ChannelOptionsBuilder($validKeys);
});

\it('removes invalid keys', function () {
    $options = ['key1' => 'value1', 'invalidKey' => 'value'];
    $expected = ['key1' => 'value1', 'key2' => null, 'key3' => null];

    $result = $this->builder->build($options);

    \expect($result)->toEqual($expected);
})->group('channelOptions');

\it('sorts options according to valid keys', function () {
    $options = ['key3' => 'value3', 'key1' => 'value1'];
    $expected = ['key1' => 'value1', 'key2' => null, 'key3' => 'value3'];

    $result = $this->builder->build($options);

    \expect($result)->toEqual($expected);
})->group('channelOptions');

\it('adds null for missing keys', function () {
    $options = ['key1' => 'value1'];
    $expected = ['key1' => 'value1', 'key2' => null, 'key3' => null];

    $result = $this->builder->build($options);

    \expect($result)->toEqual($expected);
})->group('channelOptions');
