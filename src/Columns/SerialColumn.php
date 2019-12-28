<?php

namespace Yiisoft\Yii\DataView\Columns;

/**
 * SerialColumn displays a column of row numbers (1-based).
 * To add a SerialColumn to the [[GridView]], add it to the [[GridView::columns|columns]] configuration as follows:
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         '__class' => \yii\grid\SerialColumn::class,
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 * For more details and usage information on SerialColumn, see the [guide article on data
 * widgets](guide:output-data-widgets).
 */
class SerialColumn extends Column
{
    public string $header = '#';

    protected function renderDataCellContent($model, $key, $index): string
    {
        $dataReader = $this->grid->dataReader;
        if ($dataReader !== null) {
            return $dataReader->count() + $index + 1;
        }

        return $index + 1;
    }
}
