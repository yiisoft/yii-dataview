<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;

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
 */
interface ColumnRendererInterface
{
    /**
     * Configures the column container cell.
     *
     * This method is called once per column to set up the container that will hold
     * all the column's cells. It's typically used to:
     * - Add column-wide CSS classes
     * - Set column-level HTML attributes
     * - Configure column-specific behavior
     *
     * @param ColumnInterface $column The column definition to render.
     * @param Cell $cell The cell container to configure.
     * @param GlobalContext $context Global grid rendering context.
     *
     * @return Cell The configured column container cell.
     */
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell;

    /**
     * Configures the column header cell.
     *
     * This method handles the column's header presentation, including:
     * - Header text or content
     * - Sorting indicators
     * - Filter inputs
     * - Header-specific styling
     *
     * @param ColumnInterface $column The column definition to render.
     * @param Cell $cell The header cell to configure.
     * @param HeaderContext $context Header-specific rendering context.
     *
     * @return Cell|null The configured header cell, or null if no header should be shown.
     */
    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): ?Cell;

    /**
     * Configures a data row cell.
     *
     * This method is called for each row in the grid to render the column's data cell.
     * It handles:
     * - Data value formatting
     * - Cell content generation
     * - Row-specific styling
     * - Dynamic content based on data context
     *
     * @param ColumnInterface $column The column definition to render.
     * @param Cell $cell The body cell to configure.
     * @param DataContext $context Row-specific data and rendering context.
     *
     * @return Cell The configured data cell.
     */
    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell;

    /**
     * Configures the column footer cell.
     *
     * This method handles the column's footer presentation, which might include:
     * - Summary calculations
     * - Totals
     * - Footer-specific styling
     * - Aggregated information
     *
     * @param ColumnInterface $column The column definition to render.
     * @param Cell $cell The footer cell to configure.
     * @param GlobalContext $context Global grid rendering context.
     *
     * @return Cell The configured footer cell.
     */
    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell;
}
