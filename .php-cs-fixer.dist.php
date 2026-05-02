<?php

$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__.'/src', __DIR__.'/tests'])
    ->exclude(['var', 'vendor']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP84Migration' => true,
        'declare_strict_types' => true,
        'global_namespace_import' => ['import_classes' => false, 'import_constants' => false, 'import_functions' => false],
        'phpdoc_to_comment' => false,
        'concat_space' => ['spacing' => 'one'],
        'native_function_invocation' => false,
    ])
    ->setFinder($finder);
