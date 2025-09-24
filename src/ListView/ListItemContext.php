<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\ListView;

/**
 * `ListItemContext` provides contextual information about the current item being rendered in a {@see ListView}.
 */
final class ListItemContext
{
    /**
     * @param array|object $data The current data being rendered. This can be either an array or an object
     * representing the current item's data.
     * @param int|string $key The key value associated with the current data. This is typically used as a unique
     * identifier for the item and can be either an integer or a string.
     * @param int $index The zero-based index of the data in the array. This represents the item's position
     * in the overall dataset, starting from 0.
     * @param ListView $widget The list view object that is rendering the current item. This provides access
     * to the `ListView` instance for accessing widget configuration and methods.
     */
    public function __construct(
        public readonly array|object $data,
        public readonly int|string $key,
        public readonly int $index,
        public readonly ListView $widget,
    ) {
    }
}
