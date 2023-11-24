<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use InvalidArgumentException;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;

final class SerialColumnRenderer implements ColumnRendererInterface
{
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->addAttributes($column->getColumnAttributes());
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->content($column->getHeader() ?? '#');
    }

    public function renderFilter(ColumnInterface $column, Cell $cell, GlobalContext $context): ?Cell
    {
        return null;
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $this->checkColumn($column);

        return $cell
            ->addAttributes($column->getBodyAttributes())
            ->content((string)($context->getIndex() + 1));
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->content($column->getFooter() ?? '');
    }

    /**
     * @psalm-assert SerialColumn $column
     */
    private function checkColumn(ColumnInterface $column): void
    {
        if (!$column instanceof SerialColumn) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s", but "%s" given.',
                    SerialColumn::class,
                    $column::class
                )
            );
        }
    }
}
