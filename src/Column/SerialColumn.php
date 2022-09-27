<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

/**
 * SerialColumn displays a column of row numbers (1-based).
 */
final class SerialColumn extends Column
{
    private int $offset = 0;

    /**
     * Return new instance with offset value of paginator.
     *
     * @param int $value Offset value of paginator.
     */
    public function offset(int $value): self
    {
        $new = clone $this;
        $new->offset = $value;

        return $new;
    }

    /**
     * Renders the data cell content.
     *
     * @param array|object $data The data.
     * @param mixed $key The key associated with the data.
     * @param int $index The zero-based index of the data in the data provider. {@see GridView::dataProvider}.
     *
     * @return string the rendering result.
     */
    protected function renderDataCellContent(array|object $data, mixed $key, int $index): string
    {
        return (string) ($index + 1);
    }

    protected function getLabel(): string
    {
        return parent::getLabel() !== '' ? parent::getLabel() : '#';
    }
}
