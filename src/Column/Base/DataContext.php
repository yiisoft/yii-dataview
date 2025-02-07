<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Yiisoft\Yii\DataView\Column\ColumnInterface;

/**
 * DataContext provides context information for rendering a data cell in a grid.
 *
 * This class is used to pass data and metadata about a cell being rendered to column renderers.
 * It includes information about:
 *
 * - The column being rendered
 * - The data item being displayed
 * - The key associated with the data item
 * - The zero-based index of the row
 */
final class DataContext
{
    /**
     * @param ColumnInterface $column The column being rendered.
     * @param array|object $data The data item being displayed.
     * @param int|string $key The key associated with the data item.
     * @param int $index The zero-based index of the row in the data provider.
     */
    public function __construct(
        public readonly ColumnInterface $column,
        public readonly array|object $data,
        public readonly int|string $key,
        public readonly int $index,
    ) {
    }
}
