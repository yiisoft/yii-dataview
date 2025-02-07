<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use InvalidArgumentException;
use Stringable;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;

/**
 * RadioColumnRenderer handles the rendering of radio button columns in a grid.
 *
 * This renderer is responsible for:
 * - Rendering radio input elements for each row
 * - Handling custom content generation around radio inputs
 * - Managing column, header, and cell attributes
 * - Ensuring proper HTML encoding and attribute handling
 *
 * The renderer will:
 * - Use a default name 'radio-selection' if none provided
 * - Use the row key as the radio value if none specified
 * - Support custom content generation via closure
 */
final class RadioColumnRenderer implements ColumnRendererInterface
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
     * @throws InvalidArgumentException If the column is not a RadioColumn.
     */
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->addAttributes($column->columnAttributes);
    }

    /**
     * Renders the column header if one is specified.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell container to render into.
     * @param HeaderContext $context Header rendering context.
     *
     * @return Cell|null The rendered header cell, or null if no header specified.
     *
     * @throws InvalidArgumentException If the column is not a RadioColumn.
     */
    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): ?Cell
    {
        $this->checkColumn($column);

        $header = $column->header;
        if ($header === null) {
            return null;
        }

        return $cell
            ->addAttributes($column->headerAttributes)
            ->content($header);
    }

    /**
     * Renders a radio input cell for a data row.
     *
     * This method:
     * - Creates a radio input with appropriate name and value
     * - Applies custom attributes from the column
     * - Handles custom content generation via closure
     * - Ensures proper HTML encoding
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell container to render into.
     * @param DataContext $context Data rendering context.
     *
     * @return Cell The rendered data cell.
     *
     * @throws InvalidArgumentException If the column is not a RadioColumn.
     */
    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $this->checkColumn($column);

        $inputAttributes = $column->inputAttributes;
        $name = null;
        $value = null;

        if (!array_key_exists('name', $inputAttributes)) {
            $name = 'radio-selection';
        }

        if (!array_key_exists('value', $inputAttributes)) {
            $value = $context->key;
        }

        $input = Html::radio($name, $value, $inputAttributes);

        $contentClosure = $column->content;
        /** @var string|Stringable $content */
        $content = $contentClosure === null ? $input : $contentClosure($input, $context);

        return $cell
            ->addAttributes($column->bodyAttributes)
            ->content($content)
            ->encode(false);
    }

    /**
     * Renders the column footer if one is specified.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell container to render into.
     * @param GlobalContext $context Global rendering context.
     *
     * @return Cell The rendered footer cell.
     *
     * @throws InvalidArgumentException If the column is not a RadioColumn.
     */
    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);

        if ($column->footer !== null) {
            $cell = $cell->content($column->footer);
        }

        return $cell;
    }

    /**
     * Verifies that the column is a RadioColumn instance.
     *
     * @param ColumnInterface $column The column to check.
     *
     * @throws InvalidArgumentException If the column is not a RadioColumn.
     *
     * @psalm-assert RadioColumn $column
     */
    private function checkColumn(ColumnInterface $column): void
    {
        if (!$column instanceof RadioColumn) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s", but "%s" given.',
                    RadioColumn::class,
                    $column::class
                )
            );
        }
    }
}
