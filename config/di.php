<?php

declare(strict_types=1);

use Yiisoft\Definitions\Reference;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IdMessageReader;
use Yiisoft\Translator\IntlMessageFormatter;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Yii\DataView\Column\ActionColumnRenderer;
use Yiisoft\Yii\DataView\YiiRouter\ActionColumnUrlCreator;

/** @var array $params */

return [
    ActionColumnRenderer::class => [
        '__construct()' => [
            'defaultUrlCreator' => Reference::to(ActionColumnUrlCreator::class),
        ],
    ],
    'yii.dataview.categorySource' => [
        'definition' => static function () use ($params): CategorySource {
            $reader = class_exists(MessageSource::class)
                ? new MessageSource(dirname(__DIR__) . '/messages')
                : new IdMessageReader(); // @codeCoverageIgnore

            $formatter = extension_loaded('intl')
                ? new IntlMessageFormatter()
                : new SimpleMessageFormatter();

            return new CategorySource($params['yiisoft/yii-dataview']['translation.category'], $reader, $formatter);
        },
        'tags' => ['translation.categorySource'],
    ],
];
