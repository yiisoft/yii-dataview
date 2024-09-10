<?php

namespace Yiisoft\Yii\DataView;

final class ListItemContext
{

    public function __construct(
        public readonly array|object $data,
        public readonly int|string $key,
        public readonly int $index,
        public readonly ListView $widget,
    )
    {
    }
}
