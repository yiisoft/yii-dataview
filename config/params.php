<?php

declare(strict_types=1);

use Yiisoft\Definitions\Reference;

return [
    'yiisoft/aliases' => [
        'aliases' => [
            '@gridview' => dirname(__DIR__),
        ],
    ],
    'yiisoft/translator' => [
        'categorySources' => [
            Reference::to('translator.gridview'),
        ],
    ],
];
