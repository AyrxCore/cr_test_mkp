<?php

declare(strict_types=1);

use App\Dto\News\News;
use App\Service\News\StoryblokToNewsMapper;
use App\Service\Storyblok\StoryblokRichTextResolver;
use Psr\Log\NullLogger;

uses()->group('StoryblokToNewsMapper', 'storyblok');

\beforeEach(function () {
    $this->richTextResolver = new StoryblokRichTextResolver();
    $this->logger = new NullLogger();
    $this->mapper = new StoryblokToNewsMapper($this->logger, $this->richTextResolver);
});

\it('maps a complete story with all fields', function () {
    $storyblokData = [
        'slug' => 'test-news',
        'full_slug' => 'news/test-news',
        'first_published_at' => '2025-11-26 10:00:00',
        'content' => [
            'articleTitle' => 'Test Article',
            'categoryName' => 'Innovation',
            'categoryColor' => '#FF5733',
            'articleContent' => '<p>Test content</p>',
            'ctaTxt' => 'Read more',
            'ctaLink' => 'https://example.com',
            'displayBanner' => true,
            'tags' => ['tech', 'innovation'],
            'articleImgMobile' => ['filename' => 'mobile.jpg'],
            'articleImgDesktop' => ['filename' => 'desktop.jpg'],
            'bannerImgMobile' => ['filename' => 'banner_mobile.jpg'],
            'bannerImgDesktop' => ['filename' => 'banner_desktop.jpg'],
        ],
        'tags' => ['news', 'update'],
    ];

    $result = $this->mapper->mapNews($storyblokData);

    \expect($result)->toBeInstanceOf(News::class)
        ->and($result->getSlug())->toBe('test-news')
        ->and($result->getFullSlug())->toBe('news/test-news')
        ->and($result->getFirstPublishedAt())->not->toBeNull()
        ->and($result->getArticleTitle())->toBe('Test Article')
        ->and($result->getCategoryName())->toBe('Innovation')
        ->and($result->getCategoryColor())->toBe('#FF5733')
        ->and($result->getArticleContent())->toContain('Test content')
        ->and($result->getCtaTxt())->toBe('Read more')
        ->and($result->getCtaLink())->toBe('https://example.com')
        ->and($result->getDisplayBanner())->toBeTrue()
        ->and($result->getArticleImgMobile())->not->toBeNull()
        ->and($result->getArticleImgMobile()->getFilename())->toBe('mobile.jpg')
        ->and($result->getArticleImgDesktop())->not->toBeNull()
        ->and($result->getArticleImgDesktop()->getFilename())->toBe('desktop.jpg');
});

\it('maps story with minimal fields', function () {
    $storyblokData = [
        'slug' => 'minimal-news',
        'content' => [],
    ];

    $result = $this->mapper->mapNews($storyblokData);

    \expect($result)->toBeInstanceOf(News::class)
        ->and($result->getSlug())->toBe('minimal-news')
        ->and($result->getFullSlug())->toBeNull()
        ->and($result->getFirstPublishedAt())->toBeNull();
});

\it('handles RichText structured content', function () {
    $storyblokData = [
        'slug' => 'richtext-news',
        'content' => [
            'articleContent' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            ['type' => 'text', 'text' => 'RichText paragraph'],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $result = $this->mapper->mapNews($storyblokData);

    \expect($result->getArticleContent())
        ->toContain('RichText paragraph');
});

\it('handles HTML code_block content', function () {
    $storyblokData = [
        'slug' => 'html-block-news',
        'content' => [
            'articleContent' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'code_block',
                        'attrs' => ['class' => 'language-html'],
                        'content' => [
                            ['type' => 'text', 'text' => '<h3>Custom HTML</h3><p>Custom paragraph</p>'],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $result = $this->mapper->mapNews($storyblokData);

    \expect($result->getArticleContent())
        ->toBe('<h3>Custom HTML</h3><p>Custom paragraph</p>');
});

\it('handles string articleContent', function () {
    $storyblokData = [
        'slug' => 'string-content-news',
        'content' => [
            'articleContent' => '<p>Direct HTML string</p>',
        ],
    ];

    $result = $this->mapper->mapNews($storyblokData);

    \expect($result->getArticleContent())
        ->toBe('<p>Direct HTML string</p>');
});

\it('handles null articleContent', function () {
    $storyblokData = [
        'slug' => 'no-content-news',
        'content' => [
            'articleContent' => null,
        ],
    ];

    $result = $this->mapper->mapNews($storyblokData);

    \expect($result->getArticleContent())->toBeNull();
});

\it('handles displayBanner as boolean true', function () {
    $storyblokData = [
        'slug' => 'banner-news',
        'content' => [
            'displayBanner' => true,
        ],
    ];

    $result = $this->mapper->mapNews($storyblokData);

    \expect($result->getDisplayBanner())->toBeTrue();
});

\it('handles displayBanner as boolean false', function () {
    $storyblokData = [
        'slug' => 'no-banner-news',
        'content' => [
            'displayBanner' => false,
        ],
    ];

    $result = $this->mapper->mapNews($storyblokData);

    \expect($result->getDisplayBanner())->toBeFalse();
});

\it('handles displayBanner fallback to display_banner', function () {
    $storyblokData = [
        'slug' => 'alt-banner-news',
        'content' => [
            'display_banner' => true,
        ],
    ];

    $result = $this->mapper->mapNews($storyblokData);

    \expect($result->getDisplayBanner())->toBeTrue();
});

\it('defaults displayBanner to false when not provided', function () {
    $storyblokData = [
        'slug' => 'default-banner-news',
        'content' => [],
    ];

    $result = $this->mapper->mapNews($storyblokData);

    \expect($result->getDisplayBanner())->toBeFalse();
});

\it('maps multiple stories from response', function () {
    $storyblokResponse = [
        'stories' => [
            ['slug' => 'news-1', 'content' => ['articleTitle' => 'Article 1']],
            ['slug' => 'news-2', 'content' => ['articleTitle' => 'Article 2']],
            ['slug' => 'news-3', 'content' => ['articleTitle' => 'Article 3']],
        ],
    ];

    $result = $this->mapper->mapNewsList($storyblokResponse);

    \expect($result)->toHaveCount(3)
        ->and($result[0]->getSlug())->toBe('news-1')
        ->and($result[1]->getSlug())->toBe('news-2')
        ->and($result[2]->getSlug())->toBe('news-3');
});

\it('returns empty array when no stories in response', function () {
    $storyblokResponse = ['stories' => []];

    $result = $this->mapper->mapNewsList($storyblokResponse);

    \expect($result)->toBeArray()->toBeEmpty();
});

\it('handles invalid first_published_at date gracefully', function () {
    $storyblokData = [
        'slug' => 'invalid-date-news',
        'first_published_at' => 'invalid-date-format',
        'content' => [],
    ];

    $result = $this->mapper->mapNews($storyblokData);

    // Les dates invalides doivent être rejetées et remplacées par null
    \expect($result->getFirstPublishedAt())->toBeNull();
});

\it('decodes HTML entities in rendered content', function () {
    $storyblokData = [
        'slug' => 'encoded-content',
        'content' => [
            'articleContent' => '&lt;p&gt;Encoded HTML&lt;/p&gt;',
        ],
    ];

    $result = $this->mapper->mapNews($storyblokData);

    \expect($result->getArticleContent())
        ->toBe('<p>Encoded HTML</p>');
});
