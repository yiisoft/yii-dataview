<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Yii\DataView\Column\ColumnInterface;

/**
 * DataContext provides context information for rendering a data cell in a grid.
 */
final class DataContext
{
    /**
     * @param ReadableDataInterface|null $preparedDataReader The prepared data reader with applied filters, paginating
     * and sorting. `null` means that there is no data.
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
        public readonly ?ReadableDataInterface $preparedDataReader,
        public readonly ColumnInterface $column,
        public readonly array|object $data,
        public readonly int|string $key,
        public readonly int $index,
    ) {
    }
}
