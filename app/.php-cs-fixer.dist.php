<?php

$finder = new PhpCsFixer\Finder()
    ->in(__DIR__)
    ->exclude(['var', 'migrations', 'vendor']);

return new PhpCsFixer\Config()
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setCacheFile('.php-cs-fixer.cache')
    ->setRules([
        '@PHP8x1Migration' => true,
        '@PER-CS' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'logical_operators' => false,
        'native_function_invocation' => false,
        'native_constant_invocation' => false,
        'is_null' => false,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => false,
        ],
    ])
    ->setFinder($finder);
