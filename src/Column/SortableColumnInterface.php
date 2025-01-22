<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

/**
 * @template TColumn as ColumnInterface
 */
interface SortableColumnInterface
{
    /**
     * @return array The properties that can be sorted by this column. The array keys are the logical property names,
     * and the array values are the corresponding field names.
     *
     * @psalm-param TColumn $column
     * @psalm-return array<string, string>
     */
    public function getOrderProperties(ColumnInterface $column): array;
}
