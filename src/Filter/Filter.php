<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter;

use Yiisoft\Yii\DataView\Filter\Factory\FilterFactoryInterface;
use Yiisoft\Yii\DataView\Filter\Widget\FilterWidget;

final class Filter
{
    public function __construct(
        public readonly string|FilterFactoryInterface|null $factory = null,
        public readonly FilterWidget|null $widget = null,
    ) {
    }
}
