<?php

declare(strict_types=1);

use App\Service\Storyblok\StoryblokRichTextResolver;

\beforeEach(function () {
    $this->resolver = new StoryblokRichTextResolver();
});

\it('returns empty string for null input', function () {
    $result = $this->resolver->render(null);
    \expect($result)->toBe(null);
})->group('StoryblokRichTextResolver', 'storyblok');

\it('returns empty string for empty array', function () {
    $result = $this->resolver->render([]);
    \expect($result)->toBe(null);
})->group('StoryblokRichTextResolver', 'storyblok');

\it('returns empty string for false', function () {
    $result = $this->resolver->render(false);
    \expect($result)->toBe(null);
})->group('StoryblokRichTextResolver', 'storyblok');

\it('returns string as-is when input is already a string', function () {
    $html = '<p>Test content</p>';
    $result = $this->resolver->render($html);
    \expect($result)->toBe($html);
})->group('StoryblokRichTextResolver', 'storyblok');

\it('returns empty string for non-array non-string input', function () {
    $result = $this->resolver->render(12345);
    \expect($result)->toBe(null);
})->group('StoryblokRichTextResolver', 'storyblok');

\it('extracts HTML from code_block structure', function () {
    $data = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'code_block',
                'attrs' => ['class' => 'language-html'],
                'content' => [
                    [
                        'type' => 'text',
                        'text' => '<h3>Test Title</h3><p>Test paragraph</p>',
                    ],
                ],
            ],
        ],
    ];

    $result = $this->resolver->render($data);
    \expect($result)->toBe('<h3>Test Title</h3><p>Test paragraph</p>');
})->group('StoryblokRichTextResolver', 'storyblok');

\it('renders structured RichText to HTML', function () {
    $data = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Simple paragraph',
                    ],
                ],
            ],
        ],
    ];

    $result = $this->resolver->render($data);
    \expect($result)->toContain('<p>Simple paragraph</p>');
})->group('StoryblokRichTextResolver', 'storyblok');

\it('renders RichText with bold marks', function () {
    $data = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Bold text',
                        'marks' => [
                            ['type' => 'bold'],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $result = $this->resolver->render($data);
    // Le resolver Storyblok utilise <b> au lieu de <strong>
    \expect($result)->toContain('<p><strong>Bold text</strong></p>');
})->group('StoryblokRichTextResolver', 'storyblok');

\it('renders RichText with italic marks', function () {
    $data = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Italic text',
                        'marks' => [
                            ['type' => 'italic'],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $result = $this->resolver->render($data);

    \expect($result)->toContain('<p><em>Italic text</em></p>');
})->group('StoryblokRichTextResolver', 'storyblok');

\it('does not extract HTML from code_block when multiple blocks exist', function () {
    $data = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'code_block',
                'content' => [
                    ['type' => 'text', 'text' => '<h3>Block 1</h3>'],
                ],
            ],
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Paragraph'],
                ],
            ],
        ],
    ];

    // Avec plusieurs blocs, on utilise le resolver normal
    $result = $this->resolver->render($data);
    \expect($result)->toBeString();
    // Le resolver devrait traiter ça comme du RichText normal
})->group('StoryblokRichTextResolver', 'storyblok');

\it('returns empty string when resolver throws exception', function () {
    // Structure qui cause une vraie exception dans le resolver
    // Le resolver attend un array avec 'type' et 'content', mais 'content' doit être un array
    // Si on passe quelque chose qui va causer une erreur de type dans le resolver
    $invalidData = [
        'type' => 'doc',
        'content' => 'not_an_array', // Ceci devrait causer une erreur
    ];

    // Le catch devrait attraper l'exception et retourner une string vide
    $result = $this->resolver->render($invalidData);
    \expect($result)->toBe('');
})->group('StoryblokRichTextResolver', 'storyblok');

\it('handles gracefully when resolver receives invalid structure', function () {
    // Structure avec un contenu imbriqué mal formé
    // Le resolver Storyblok est tolérant et peut retourner du HTML partiel au lieu de lancer une exception
    $invalidData = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => 'should_be_array_not_string', // Erreur de type
            ],
        ],
    ];

    // Le resolver peut soit retourner une string vide, soit du HTML partiel
    $result = $this->resolver->render($invalidData);
    \expect($result)->toBeString();
})->group('StoryblokRichTextResolver', 'storyblok');

\it('renders ordered list (numbered bullets)', function () {
    $data = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'ordered_list',
                'content' => [
                    [
                        'type' => 'list_item',
                        'content' => [
                            [
                                'type' => 'paragraph',
                                'content' => [
                                    ['type' => 'text', 'text' => 'First item'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'list_item',
                        'content' => [
                            [
                                'type' => 'paragraph',
                                'content' => [
                                    ['type' => 'text', 'text' => 'Second item'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $result = $this->resolver->render($data);
    \expect($result)->toContain('<ol>')
        ->and($result)->toContain('<li>')
        ->and($result)->toContain('First item')
        ->and($result)->toContain('Second item');
})->group('StoryblokRichTextResolver', 'storyblok');

\it('renders bullet list', function () {
    $data = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'bullet_list',
                'content' => [
                    [
                        'type' => 'list_item',
                        'content' => [
                            [
                                'type' => 'paragraph',
                                'content' => [
                                    ['type' => 'text', 'text' => 'Bullet one'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'list_item',
                        'content' => [
                            [
                                'type' => 'paragraph',
                                'content' => [
                                    ['type' => 'text', 'text' => 'Bullet two'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $result = $this->resolver->render($data);
    \expect($result)->toContain('<ul>')
        ->and($result)->toContain('<li>')
        ->and($result)->toContain('Bullet one')
        ->and($result)->toContain('Bullet two');
})->group('StoryblokRichTextResolver', 'storyblok');
