<?php

declare(strict_types=1);

use App\Dto\LegalContent;
use App\Service\Cgu\StoryblokToLegalContentMapper;
use App\Service\Storyblok\StoryblokRichTextResolver;

uses()->group('StoryblokToLegalContentMapper', 'legal_content', 'storyblok');

\beforeEach(function () {
    $this->richTextResolver = new StoryblokRichTextResolver();
    $this->mapper = new StoryblokToLegalContentMapper($this->richTextResolver);
});

// --- mapByChannelCode ---

\it('returns the matching CGU when the story name equals the channel code', function () {
    $response = [
        'stories' => [
            ['name' => 'other-channel', 'content' => ['cgu' => 'Other CGU']],
            ['name' => 'my-channel', 'content' => ['cgu' => 'My CGU']],
        ],
    ];

    $result = $this->mapper->mapByChannelCode($response, 'my-channel');

    \expect($result)->toBeInstanceOf(LegalContent::class)
        ->and($result->getCgu())->toBe('My CGU');
});

\it('returns null when no story name matches the channel code', function () {
    $response = [
        'stories' => [
            ['name' => 'other-channel', 'content' => []],
        ],
    ];

    $result = $this->mapper->mapByChannelCode($response, 'unknown-channel');

    \expect($result)->toBeNull();
});

\it('returns null when stories list is empty', function () {
    $result = $this->mapper->mapByChannelCode(['stories' => []], 'my-channel');

    \expect($result)->toBeNull();
});

\it('returns null when stories key is missing from response', function () {
    $result = $this->mapper->mapByChannelCode([], 'my-channel');

    \expect($result)->toBeNull();
});

// --- mapCgu ---

\it('maps all three content fields as plain strings', function () {
    $story = [
        'name' => 'my-channel',
        'content' => [
            'cgu' => '<p>CGU content</p>',
            'legalTerms' => '<p>Legal terms</p>',
            'privacyPolicy' => '<p>Privacy policy</p>',
        ],
    ];

    $result = $this->mapper->mapLegalContent($story);

    \expect($result)->toBeInstanceOf(LegalContent::class)
        ->and($result->getCgu())->toBe('<p>CGU content</p>')
        ->and($result->getLegalTerms())->toBe('<p>Legal terms</p>')
        ->and($result->getPrivacyPolicy())->toBe('<p>Privacy policy</p>');
});

\it('maps null when content fields are absent', function () {
    $result = $this->mapper->mapLegalContent(['name' => 'my-channel', 'content' => []]);

    \expect($result->getCgu())->toBeNull()
        ->and($result->getLegalTerms())->toBeNull()
        ->and($result->getPrivacyPolicy())->toBeNull();
});

\it('maps null when content key is missing', function () {
    $result = $this->mapper->mapLegalContent(['name' => 'my-channel']);

    \expect($result->getCgu())->toBeNull()
        ->and($result->getLegalTerms())->toBeNull()
        ->and($result->getPrivacyPolicy())->toBeNull();
});

\it('resolves RichText structured content for cgu field', function () {
    $story = [
        'name' => 'my-channel',
        'content' => [
            'cgu' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            ['type' => 'text', 'text' => 'RichText CGU'],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $result = $this->mapper->mapLegalContent($story);

    \expect($result->getCgu())->toContain('RichText CGU');
});

\it('returns plain string content as-is without decoding', function () {
    $story = [
        'name' => 'my-channel',
        'content' => [
            'cgu' => '&lt;p&gt;Raw HTML string&lt;/p&gt;',
        ],
    ];

    $result = $this->mapper->mapLegalContent($story);

    \expect($result->getCgu())->toBe('&lt;p&gt;Raw HTML string&lt;/p&gt;');
});

\it('decodes HTML entities in rendered RichText array content', function () {
    $story = [
        'name' => 'my-channel',
        'content' => [
            'cgu' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            ['type' => 'text', 'text' => 'Texte avec accent : é'],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $result = $this->mapper->mapLegalContent($story);

    \expect($result->getCgu())->toContain('é');
});

\it('returns null for non-string non-array content value', function () {
    $story = [
        'name' => 'my-channel',
        'content' => [
            'cgu' => 42,
        ],
    ];

    $result = $this->mapper->mapLegalContent($story);

    \expect($result->getCgu())->toBeNull();
});


