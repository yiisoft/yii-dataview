<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\GridView\Column;

use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Yii\DataView\GridView\Column\Base\Cell;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\Base\GlobalContext;

/**
 * `SerialColumnRenderer` handles the rendering of sequential row number columns in a grid.
 *
 * @implements ColumnRendererInterface<SerialColumn>
 */
final class SerialColumnRenderer implements ColumnRendererInterface
{
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        return $cell->addAttributes($column->columnAttributes);
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        return $cell->content($column->header ?? '#');
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $index = $context->preparedDataReader instanceof OffsetPaginator
            ? $context->preparedDataReader->getOffset() + $context->index + 1
            : $context->index + 1;

        return $cell
            ->addAttributes($column->bodyAttributes)
            ->content((string) $index);
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        return $cell->content($column->footer ?? '');
    }
}
