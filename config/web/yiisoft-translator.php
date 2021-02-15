<?php

declare(strict_types=1);

use Yiisoft\Factory\Definitions\Reference;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;

return [
    CategorySource::class => [
        '__class' => CategorySource::class,
        '__construct()' => [
            'name' => 'yii-gridview',
        ],
    ],

    TranslatorInterface::class => [
        '__class' => Translator:: class,
        'addCategorySource()' => [Reference::to(CategorySource::class)],
    ],
];
