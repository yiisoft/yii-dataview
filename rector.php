<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\Catch_\RemoveUnusedVariableInCatchRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets(php81: true)
    ->withRules([
        InlineConstructorDefaultToPropertyRector::class,
    ])
    ->withSkip([
        ClosureToArrowFunctionRector::class,
        NullToStrictStringFuncCallArgRector::class,
        RemoveUnusedVariableInCatchRector::class => [
            __DIR__ . '/src/BaseListView.php',
        ],
    ]);
