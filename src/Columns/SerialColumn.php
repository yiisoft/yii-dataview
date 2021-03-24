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

    /**
     * Renders the data cell content.
     *
     * @param array|object $model the data model.
     * @param mixed $key the key associated with the data model.
     * @param int $index the zero-based index of the data model among the models array returned by
     * {@see GridView::dataReader}.
     *
     * @return string the rendering result.
     */
    protected function renderDataCellContent($model, $key, int $index): string
    {
        $paginator = $this->grid->getPaginator();
        $row = $paginator->getOffSet() + $index + 1;
        return (string) $row;
    }
}
