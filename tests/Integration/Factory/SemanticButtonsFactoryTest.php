<?php

declare(strict_types=1);

use App\Dto\SemanticButton;
use App\Factory\SemanticButtonFactory;
use Psr\Cache\CacheItemPoolInterface;
use Zenstruck\Foundry\Test\ResetDatabase;

\uses(ResetDatabase::class);

\it('creates a SemanticButton and caches it', function () {
    $adapterInterface = $this->container->get(CacheItemPoolInterface::class);
    $adapterInterface->reset();

    $factory = new SemanticButtonFactory($adapterInterface);

    $data = [
        'id' => 123,
        'dynamic_fields' => [
            [
                'dynamic_field_configuration' => ['name' => ['default' => 'sem_btn_homepage_label']],
                'value' => 'Test Label',
            ],
            [
                'dynamic_field_configuration' => ['name' => ['default' => 'sem_btn_homepage_search']],
                'value' => 'test_search_query',
            ],
            [
                'dynamic_field_configuration' => ['name' => ['default' => 'sem_btn_homepage_section_title']],
                'value' => 'Test Section Title',
            ],
        ],
    ];

    $result = $factory->create($data);

    \expect($result)->toBeInstanceOf(SemanticButton::class);
    \expect($result->getLabel())
        ->toBe('Test Label');
    \expect($result->getSearch())
        ->toBe('test_search_query');
    \expect($result->getSectionTitle())
        ->toBe('Test Section Title');
})->group('IntegrationSemanticButtonFactory');
