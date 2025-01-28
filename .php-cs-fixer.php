<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
;

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setCacheFile(__DIR__ . '/vendor/.cache/php-cs-fixer')
    ->setRules([
        '@PhpCsFixer' => true,
        'concat_space' => ['spacing' => 'one'],
        'fully_qualified_strict_types' => false, // We have chosen to only import base namespace.
        'increment_style' => false,
        'method_chaining_indentation' => false, // to keep our test the format we want it we must disable this rule.
        'no_unneeded_control_parentheses' => [
            'statements' => [
                'break',
                'clone',
                'continue',
                'echo_print',
                'others',
                'return',
                'switch_case',
                'yield',
                'yield_from',
            ],
        ],
        'ordered_types' => ['null_adjustment' => 'always_last'],
        'php_unit_test_class_requires_covers' => false,
        'phpdoc_align' => false,
        'phpdoc_annotation_without_dot' => false, // In our case we have decided that anotations are a sentence.
        'phpdoc_no_alias_tag' => false, // We want to have our property-read tags
        'phpdoc_order' => ['order' => ['uses', 'param', 'return', 'throws']],
        'phpdoc_scalar' => false, // I'd like it to be true,but that's not compatible with our docscript.
        'phpdoc_separation' => ['groups' => [
            ['Annotation', 'NamedArgumentConstructor', 'Target'],
            ['author', 'copyright', 'license'],
            ['category', 'package', 'subpackage'],
            ['deprecated', 'link', 'see', 'since'],
            ['return', 'throws'],
        ]], // We override the default config to separate between property and property-read
        'phpdoc_to_comment' => ['ignored_tags' => ['var', 'phpstan-var']],
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
        'string_implicit_backslashes' => ['single_quoted' => 'escape'],
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
    ])
    ->setFinder($finder)
;
