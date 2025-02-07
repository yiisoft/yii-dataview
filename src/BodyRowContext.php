<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

/**
 * BodyRowContext provides contextual information for rendering a table body row in data views.
 *
 * This immutable class encapsulates the data and metadata needed when rendering individual rows
 * in data view widgets (such as GridView). It is particularly useful when customizing row
 * attributes or implementing conditional row rendering.
 *
 * The context includes:
 * - The data for the current row
 * - A unique key identifying the row
 * - The zero-based index of the row in the dataset
 *
 * Common use cases:
 * - Conditional row styling
 * - Row-specific attribute generation
 * - Alternate row formatting
 * - Row-level access control
 *
 * Example usage:
 * ```php
 * // Using with GridView for conditional row styling
 * $gridView = GridView::widget()
 *     ->bodyRowAttributes(function (BodyRowContext $context) {
 *         // Add 'highlighted' class for specific items
 *         if ($context->data->isHighlighted) {
 *             return ['class' => 'highlighted'];
 *         }
 *
 *         // Add 'alternate' class for even rows
 *         if ($context->index % 2 === 0) {
 *             return ['class' => 'alternate'];
 *         }
 *
 *         return [];
 *     });
 *
 * // Using for row-level access control
 * $gridView = GridView::widget()
 *     ->bodyRowAttributes(function (BodyRowContext $context) {
 *         // Hide rows based on permissions
 *         if (!$currentUser->canView($context->data)) {
 *             return ['style' => 'display: none'];
 *         }
 *
 *         return [];
 *     });
 * ```
 */
final class BodyRowContext
{
    /**
     * Creates a new instance of body row context.
     *
     * @param array|object $data The data for the current row. Can be either an array or an object
     *                          containing the row's data.
     * @param int|string $key The unique identifier for the row. This can be either an integer
     *                       or a string that uniquely identifies the row in the dataset.
     * @param int $index The zero-based index of the row in the dataset. This represents the
     *                  row's position in the current page of results.
     */
    public function __construct(
        public readonly array|object $data,
        public readonly int|string $key,
        public readonly int $index,
    ) {
    }
}
