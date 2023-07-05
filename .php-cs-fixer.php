<?php

declare(strict_types=1);

return (new PhpCsFixer\Config())
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude('tests')
            ->ignoreVCSIgnored(true)
    )
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'php_unit_construct' => true,
        'php_unit_strict' => true,
        'strict_param' => true,
        'ordered_imports' => true,
        'declare_strict_types' => true,
        'native_function_invocation' => ['include' => ['@all']],
        'native_constant_invocation' => true,
        'protected_to_private' => true,
        'class_definition' => ['multi_line_extends_each_single_line' => true],
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_first', 'sort_algorithm' => 'none'],
        'ordered_class_elements' => true,
        'single_line_throw' => false,
        'phpdoc_to_comment' => false, // needed for some phpstan annotation
        'visibility_required' => ['elements' => ['property', 'method', 'const']],
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
    ]);
