<?php

declare(strict_types=1);

\it('gets categories collection from Djust API', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/categories', [
        'headers' => [
            'Accept' => 'application/json',
        ],
    ]);

    $this->assertResponseIsSuccessful();

    $categories = $client->getResponse()->toArray();
    \expect($categories)->toBeArray();
    \expect($categories)->not->toBeEmpty();
})->group('CategoryProvider');

\it('returns categories with nested children', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/categories', [
        'headers' => [
            'Accept' => 'application/json',
        ],
    ]);

    $this->assertResponseIsSuccessful();

    $categories = $client->getResponse()->toArray();

    // Find a category with children
    $categoryWithChildren = null;
    foreach ($categories as $category) {
        if (!empty($category['children'])) {
            $categoryWithChildren = $category;
            break;
        }
    }

    \expect($categoryWithChildren['children'])->toBeArray();
    \expect($categoryWithChildren['children'])->not->toBeEmpty();

    $firstChild = $categoryWithChildren['children'][0];
    \expect($firstChild)->toHaveKey('id');
    \expect($firstChild)->toHaveKey('name');
    \expect($firstChild)->toHaveKey('parentId');
    \expect($firstChild['parentId'])->toBe($categoryWithChildren['id']);
})->group('CategoryProvider');

\it('maintains parent-child relationships through all nesting levels', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/categories', [
        'headers' => [
            'Accept' => 'application/json',
        ],
    ]);

    $this->assertResponseIsSuccessful();

    $categories = $client->getResponse()->toArray();

    // Check recursive parent-child relationships
    foreach ($categories as $category) {
        if (!empty($category['children'])) {
            foreach ($category['children'] as $child) {
                \expect($child['parentId'])->toBe($category['id']);

                // Check nested children as well
                if (!empty($child['children'])) {
                    foreach ($child['children'] as $grandchild) {
                        \expect($grandchild['parentId'])->toBe($child['id']);
                    }
                }
            }
        }
    }
})->group('CategoryProvider');
