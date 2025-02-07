<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use InvalidArgumentException;
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
     * @return Cell The rendered cell.
     *
     * @throws InvalidArgumentException If the column is not a SerialColumn.
     */
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->addAttributes($column->columnAttributes);
    }

    /**
     * Renders the column header with default '#' if none specified.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell container to render into.
     * @param HeaderContext $context Header rendering context.
     *
     * @return Cell The rendered header cell.
     *
     * @throws InvalidArgumentException If the column is not a SerialColumn.
     */
    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): Cell
    {
        $this->checkColumn($column);
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
     * @return Cell The rendered data cell.
     *
     * @throws InvalidArgumentException If the column is not a SerialColumn.
     */
    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $this->checkColumn($column);

        return $cell
            ->addAttributes($column->bodyAttributes)
            ->content((string)($context->index + 1));
    }

    /**
     * Renders the column footer with empty string as default.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell container to render into.
     * @param GlobalContext $context Global rendering context.
     *
     * @return Cell The rendered footer cell.
     *
     * @throws InvalidArgumentException If the column is not a SerialColumn.
     */
    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->content($column->footer ?? '');
    }

    /**
     * Verifies that the column is a SerialColumn instance.
     *
     * @param ColumnInterface $column The column to check.
     *
     * @throws InvalidArgumentException If the column is not a SerialColumn.
     *
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
