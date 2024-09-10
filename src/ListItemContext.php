<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

final class ListItemContext
{
    /**
     * @param array|object $data The current data being rendered.
     * @param int|string $key The key value associated with the current data.
     * @param int $index The zero-based index of the data in the array.
     * @param ListView $widget The list view object.
     */
    public function __construct(
        public readonly array|object $data,
        public readonly int|string $key,
        public readonly int $index,
        public readonly ListView $widget,
    ) {
    }
}
