<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;

/**
 * @implements ColumnRendererInterface<SerialColumn>
 */
final class SerialColumnRenderer implements ColumnRendererInterface
{
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        return $cell->addAttributes($column->columnAttributes);
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): Cell
    {
        return $cell->content($column->header ?? '#');
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        return $cell
            ->addAttributes($column->bodyAttributes)
            ->content((string)($context->index + 1));
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        return $cell->content($column->footer ?? '');
    }
}
