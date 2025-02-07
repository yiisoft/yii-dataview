<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Yiisoft\Yii\DataView\Column\ColumnInterface;

/**
 * DataContext provides context information for rendering a data cell in a grid.
 *
 * This immutable class encapsulates all necessary information needed by column renderers
 * to properly render a data cell within a grid. It is passed to the renderBody() method
 * of column renderers and provides access to both the data being displayed and metadata
 * about its position in the grid.
 *
 * The context includes:
 * - Column definition being rendered
 * - Current data item (row data)
 * - Unique key for the data item
 * - Row index in the dataset
 *
 * Common use cases:
 * - Accessing row data for cell content
 * - Generating row-specific formatting
 * - Creating row-dependent links
 * - Implementing row-level access control
 *
 * Example usage:
 * ```php
 * class CustomColumnRenderer implements ColumnRendererInterface
 * {
 *     public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
 *     {
 *         // Access data properties
 *         $value = $context->data->propertyName;
 *
 *         // Use row index for alternate styling
 *         $class = $context->index % 2 === 0 ? 'even' : 'odd';
 *
 *         // Create row-specific links
 *         $url = "/view/{$context->key}";
 *
 *         return $cell
 *             ->content(Html::a($value, $url))
 *             ->addClass($class);
 *     }
 * }
 * ```
 */
final class DataContext
{
    /**
     * Creates a new data context instance.
     *
     * @param ColumnInterface $column The column being rendered. This provides access to
     * the column configuration and behavior definitions.
     * @param array|object $data The data item being displayed. This can be either an array
     * or an object containing the row's data.
     * @param int|string $key The key associated with the data item. This uniquely identifies
     * the row in the dataset and can be used for generating URLs or implementing row operations.
     * @param int $index The zero-based index of the row in the data provider. This can be
     * used for implementing alternate row styling or position-based logic.
     */
    public function __construct(
        public readonly ColumnInterface $column,
        public readonly array|object $data,
        public readonly int|string $key,
        public readonly int $index,
    ) {
    }
}
