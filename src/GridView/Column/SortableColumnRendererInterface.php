<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\GridView\Column;

/**
 * Interface for column renderers that support sorting functionality.
 *
 * This interface defines the contract for column renderers that need to provide
 * sorting capabilities. It allows columns to specify which properties can be sorted.
 *
 * @template TColumn as ColumnInterface
 */
interface SortableColumnRendererInterface
{
    /**
     * Gets the properties that can be sorted by this column.
     * For example:
     *
     * ```php
     * ['fullName', 'joinDate']
     * ```
     *
     * @param ColumnInterface $column The column instance being rendered.
     *
     * @return array The properties that can be sorted by this column.
     *
     * @psalm-param TColumn $column
     * @psalm-return list<string>
     */
    public function getOrderProperties(ColumnInterface $column): array;
}
