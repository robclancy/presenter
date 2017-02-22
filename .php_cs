<?php

$finder = PhpCsFixer\Finder::create()->in(__DIR__.'/src');

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        'not_operator_with_successor_space' => true,
        'array_syntax' => true,
    ])
    ->setUsingCache(true)
    ->setFinder($finder);
