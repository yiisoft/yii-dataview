<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

/**
 * ListItemContext provides contextual information about the current item being rendered in a ListView.
 *
 * This class encapsulates all necessary information about a list item during the rendering process,
 * making it easier to access item-specific data and metadata when customizing item rendering.
 *
 * The context includes:
 * - The current data item being rendered
 * - The unique key associated with the data item
 * - The zero-based index of the item in the data set
 * - A reference to the ListView widget instance
 *
 * This context is particularly useful when:
 * - Customizing item rendering based on position (first, last, odd/even)
 * - Accessing item data and metadata in custom rendering functions
 * - Implementing conditional rendering logic
 * - Generating unique identifiers for items
 *
 * Example usage:
 * ```php
 * // Custom item rendering using context
 * $listView->itemView(function (ListItemContext $context) {
 *     $isEven = $context->index % 2 === 0;
 *     $class = $isEven ? 'even' : 'odd';
 *
 *     return Html::div()
 *         ->class($class)
 *         ->content(
 *             "Item #{$context->index}: " .
 *             Html::encode($context->data->title)
 *         )
 *         ->render();
 * });
 * ```
 */
final class ListItemContext
{
    /**
     * @param array|object $data The current data being rendered. This can be either an array or an object
     *                          representing the current item's data.
     * @param int|string $key The key value associated with the current data. This is typically used as a unique
     *                       identifier for the item and can be either an integer or a string.
     * @param int $index The zero-based index of the data in the array. This represents the item's position
     *                  in the overall dataset, starting from 0.
     * @param ListView $widget The list view object that is rendering the current item. This provides access
     *                        to the ListView instance for accessing widget configuration and methods.
     */
    public function __construct(
        public readonly array|object $data,
        public readonly int|string $key,
        public readonly int $index,
        public readonly ListView $widget,
    ) {
    }
}
