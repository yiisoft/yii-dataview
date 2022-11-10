<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\Translator\SimpleMessageFormatter;

/** @var array $params */

return [
    'translator.dataview' => [
        'definition' => static function (Aliases $aliases) use ($params) {
            $messageReader = new MessageSource($aliases->get('@yii-dataview/resources/messages'));

            return new CategorySource($params['yiisoft/translator']['dataviewCategory'], $messageReader, new SimpleMessageFormatter());
        },
        'tags' => ['translator.categorySource']
    ],
];
