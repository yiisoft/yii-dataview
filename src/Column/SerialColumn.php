<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

/**
 * SerialColumn displays a column of row numbers (1-based).
 */
final class SerialColumn extends Column
{
    protected function getLabel(): string
    {
        return parent::getLabel() !== '' ? parent::getLabel() : '#';
    }

    /**
     * Renders the data cell content.
     *
     * @param array|object $data The data.
     * @param mixed $key The key associated with the data.
     * @param int $index The zero-based index of the data in the data provider. {@see GridView::dataProvider}.
     */
    protected function renderDataCellContent(array|object $data, mixed $key, int $index): string
    {
        return (string) ($index + 1);
    }
}
