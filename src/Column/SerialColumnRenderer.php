<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use InvalidArgumentException;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;

/**
 * SerialColumnRenderer handles the rendering of sequential row number columns in a grid.
 *
 * This renderer is responsible for:
 * - Rendering sequential numbers (1-based) for each row
 * - Managing column and cell attributes
 * - Providing default header ('#') and footer content
 * - Ensuring proper type checking and validation
 *
 * @implements ColumnRendererInterface<SerialColumn>
 */
final class SerialColumnRenderer implements ColumnRendererInterface
{
    /**
     * Renders the column container with attributes.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell container to render into.
     * @param GlobalContext $context Global rendering context.
     *
     * @throws InvalidArgumentException If the column is not a SerialColumn.
     * @return Cell The rendered cell.
     */
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        return $cell->addAttributes($column->columnAttributes);
    }

    /**
     * Renders the column header with default '#' if none specified.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell container to render into.
     * @param HeaderContext $context Header rendering context.
     *
     * @throws InvalidArgumentException If the column is not a SerialColumn.
     * @return Cell The rendered header cell.
     */
    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): Cell
    {
        return $cell->content($column->header ?? '#');
    }

    /**
     * Renders a cell containing the current row number (1-based).
     *
     * The row number is calculated by adding 1 to the current row index,
     * ensuring a user-friendly display starting from 1 instead of 0.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell container to render into.
     * @param DataContext $context Data rendering context containing the row index.
     *
     * @throws InvalidArgumentException If the column is not a SerialColumn.
     * @return Cell The rendered data cell.
     */
    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $index = $context->preparedDataReader instanceof OffsetPaginator
            ? $context->preparedDataReader->getOffset() + $context->index + 1
            : $context->index + 1;

        return $cell
            ->addAttributes($column->bodyAttributes)
            ->content((string) $index);
    }

    /**
     * Renders the column footer with empty string as default.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell container to render into.
     * @param GlobalContext $context Global rendering context.
     *
     * @throws InvalidArgumentException If the column is not a SerialColumn.
     * @return Cell The rendered footer cell.
     */
    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        return $cell->content($column->footer ?? '');
    }
}
