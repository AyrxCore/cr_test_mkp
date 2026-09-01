<?php

declare(strict_types=1);

use App\Dto\SemanticButton\SemanticButton;
use App\Service\SemanticButton\StoryblokToSemanticButtonMapper;

uses()->group('StoryblokToSemanticButtonMapper', 'semantic_button', 'storyblok');

\beforeEach(function () {
    $this->mapper = new StoryblokToSemanticButtonMapper();
});

// --- mapByChannelCode ---

\it('returns the semantic buttons of the story matching the channel code', function () {
    $response = [
        'stories' => [
            ['name' => 'OTHER_CHANNEL', 'content' => ['semanticButton' => [['label' => 'Other', 'search' => 'other']]]],
            ['name' => 'MY_CHANNEL', 'content' => ['semanticButton' => [['label' => 'Mine', 'search' => 'mine']]]],
        ],
    ];

    $result = $this->mapper->mapByChannelCode($response, 'MY_CHANNEL');

    \expect($result)->toBeArray()->toHaveCount(1)
        ->and($result[0])->toBeInstanceOf(SemanticButton::class)
        ->and($result[0]->getLabel())->toBe('Mine')
        ->and($result[0]->getSearch())->toBe('mine');
});

\it('returns an empty array when no story name matches the channel code', function () {
    $response = [
        'stories' => [
            ['name' => 'OTHER_CHANNEL', 'content' => []],
        ],
    ];

    $result = $this->mapper->mapByChannelCode($response, 'UNKNOWN_CHANNEL');

    \expect($result)->toBeArray()->toBeEmpty();
});

\it('returns an empty array when stories list is empty', function () {
    $result = $this->mapper->mapByChannelCode(['stories' => []], 'MY_CHANNEL');

    \expect($result)->toBeArray()->toBeEmpty();
});

\it('returns an empty array when stories key is missing from response', function () {
    $result = $this->mapper->mapByChannelCode([], 'MY_CHANNEL');

    \expect($result)->toBeArray()->toBeEmpty();
});

// --- mapSemanticButtons ---

\it('maps the section title as the first item when present', function () {
    $story = [
        'name' => 'MY_CHANNEL',
        'content' => [
            'sectionTitle' => 'Nos univers',
            'semanticButton' => [
                ['label' => 'Bureautique', 'search' => 'bureautique'],
            ],
        ],
    ];

    $result = $this->mapper->mapSemanticButtons($story);

    \expect($result)->toHaveCount(2)
        ->and($result[0]->getId())->toBe(0)
        ->and($result[0]->getSectionTitle())->toBe('Nos univers')
        ->and($result[0]->getLabel())->toBeNull()
        ->and($result[0]->getSearch())->toBeNull()
        ->and($result[1]->getId())->toBe(1)
        ->and($result[1]->getLabel())->toBe('Bureautique')
        ->and($result[1]->getSearch())->toBe('bureautique')
        ->and($result[1]->getSectionTitle())->toBeNull();
});

\it('does not add a section title item when sectionTitle is absent', function () {
    $story = [
        'name' => 'MY_CHANNEL',
        'content' => [
            'semanticButton' => [
                ['label' => 'Bureautique', 'search' => 'bureautique'],
            ],
        ],
    ];

    $result = $this->mapper->mapSemanticButtons($story);

    \expect($result)->toHaveCount(1)
        ->and($result[0]->getId())->toBe(0)
        ->and($result[0]->getSectionTitle())->toBeNull();
});

\it('does not add a section title item when sectionTitle is an empty string', function () {
    $story = [
        'name' => 'MY_CHANNEL',
        'content' => [
            'sectionTitle' => '',
            'semanticButton' => [],
        ],
    ];

    $result = $this->mapper->mapSemanticButtons($story);

    \expect($result)->toBeArray()->toBeEmpty();
});

\it('maps multiple semantic buttons keeping incrementing ids', function () {
    $story = [
        'name' => 'MY_CHANNEL',
        'content' => [
            'semanticButton' => [
                ['label' => 'Bureautique', 'search' => 'bureautique'],
                ['label' => 'Mobilier', 'search' => 'mobilier'],
            ],
        ],
    ];

    $result = $this->mapper->mapSemanticButtons($story);

    \expect($result)->toHaveCount(2)
        ->and($result[0]->getId())->toBe(0)
        ->and($result[1]->getId())->toBe(1);
});

\it('returns an empty array when content key is missing', function () {
    $result = $this->mapper->mapSemanticButtons(['name' => 'MY_CHANNEL']);

    \expect($result)->toBeArray()->toBeEmpty();
});

\it('ignores non-array items within the semanticButton section', function () {
    $story = [
        'name' => 'MY_CHANNEL',
        'content' => [
            'semanticButton' => ['not-an-array', ['label' => 'Bureautique', 'search' => 'bureautique']],
        ],
    ];

    $result = $this->mapper->mapSemanticButtons($story);

    \expect($result)->toHaveCount(1)
        ->and($result[0]->getLabel())->toBe('Bureautique');
});
