<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Columns;

/**
 * SerialColumn displays a column of row numbers (1-based).
 *
 * To add a SerialColumn to the {@see GridView}, add it to the {@see GridView::columns|columns} configuration as
 * follows:
 *
 * ```php
 * [
 *     '__class' => SerialColumn::class,
 * ],
 * ```
 * For more details and usage information on SerialColumn:
 * see the [guide article on data widgets](guide:output-data-widgets).
 */
final class SerialColumn extends Column
{
    protected string $header = '#';

    protected function renderDataCellContent(array $model, $key, int $index): string
    {
        $paginator = $this->grid->getPaginator();
        $row = $index + 1;

        if ($paginator !== null) {
            $row = $paginator->getOffSet() + $index + 1;
        }

        return (string) $row;
    }
}
