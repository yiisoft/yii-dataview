<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\DataContext;

/**
 * Interface for column renderers that handle the visual presentation of grid columns.
 *
 * Column renderers are responsible for transforming column definitions into HTML output
 * by configuring cells for different parts of the column (container, header, body, footer).
 * Each renderer works with a specific column type and handles its unique rendering requirements.
 *
 * Features:
 * - Column container configuration
 * - Header cell rendering with optional visibility
 * - Data cell rendering with row context
 * - Footer cell rendering
 * - Context-aware rendering decisions
 *
 * Example implementation:
 * ```php
 * class CustomColumnRenderer implements ColumnRendererInterface
 * {
 *     public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
 *     {
 *         return $cell->addClass('custom-column');
 *     }
 *
 *     public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): ?Cell
 *     {
 *         return $cell->content('Custom Header');
 *     }
 *
 *     public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
 *     {
 *         return $cell->content($context->data['value']);
 *     }
 *
 *     public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
 *     {
 *         return $cell->content('Total');
 *     }
 * }
 * ```
 *
 * @see Cell The cell object that gets configured by the renderer
 * @see ColumnInterface The column definition interface
 *
 * @template TColumn as ColumnInterface
 */
interface ColumnRendererInterface
{
    /**
     * Configures the column container cell.
     *
     * This method is called once per column to set up the container that will hold
     * all the column's cells.
     *
     * @param ColumnInterface $column The column definition to render.
     * @psalm-param TColumn $column
     * @param Cell $cell The cell container to configure.
     * @param GlobalContext $context Global grid rendering context.
     *
     * @return Cell The configured column container cell.
     */
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell;

    /**
     * Configures the column header cell.
     *
     * @param ColumnInterface $column The column definition to render.
     * @psalm-param TColumn $column
     * @param Cell $cell The header cell to configure.
     * @param GlobalContext $context Global grid rendering context.
     *
     * @return Cell|null The configured header cell, or `null` if no header should be shown.
     */
    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): ?Cell;

    /**
     * Configures a data row cell.
     *
     * This method is called for each row in the grid to render the column's data cell.
     *
     * @param ColumnInterface $column The column definition to render.
     * @psalm-param TColumn $column
     * @param Cell $cell The body cell to configure.
     * @param DataContext $context Row-specific data and rendering context.
     *
     * @return Cell The configured data cell.
     */
    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell;

    /**
     * Configures the column footer cell.
     *
     * @param ColumnInterface $column The column definition to render.
     * @psalm-param TColumn $column
     * @param Cell $cell The footer cell to configure.
     * @param GlobalContext $context Global grid rendering context.
     *
     * @return Cell The configured footer cell.
     */
    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell;
}
