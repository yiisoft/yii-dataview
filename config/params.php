<?php

declare(strict_types=1);

use Yiisoft\Definitions\Reference;

return [
    'yiisoft/aliases' => [
        'aliases' => [
            '@yii-dataview' => dirname(__DIR__),
        ],
    ],
    'yiisoft/translator' => [
        'categorySources' => [
            Reference::to('translator.dataview'),
        ],
    ],
];
