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
 * CheckboxColumnRenderer renders checkbox inputs in a grid column.
 *
 * This class handles:
 * - Rendering checkbox inputs with customizable attributes
 * - Supporting both single and multiple selection modes
 * - Managing header, body, and footer cell content
 * - Handling custom content generation
 */
final class CheckboxColumnRenderer implements ColumnRendererInterface
{
    /**
     * Render the column container.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell to render.
     * @param GlobalContext $context Global rendering context.
     *
     * @throws InvalidArgumentException If the column is not a CheckboxColumn.
     * @return Cell The rendered cell.
     */
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->addAttributes($column->columnAttributes);
    }

    /**
     * Render the column header.
     *
     * For multiple selection mode, renders a "select all" checkbox if no header content is specified.
     * For single selection mode, returns null if no header content is specified.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell to render.
     * @param HeaderContext $context Header rendering context.
     *
     * @throws InvalidArgumentException If the column is not a CheckboxColumn.
     * @return Cell|null The rendered header cell or null for empty single-selection headers.
     */
    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): ?Cell
    {
        $this->checkColumn($column);

        $header = $column->header;
        if ($header === null) {
            if (!$column->multiple) {
                return null;
            }
            $header = Html::checkbox('checkbox-selection-all', 1);
        }

        return $cell
            ->addAttributes($column->headerAttributes)
            ->content($header);
    }

    /**
     * Render a data cell in the column.
     *
     * Generates a checkbox input with:
     * - Default or custom name attribute
     * - Default or custom value (using row key)
     * - Configurable input attributes
     * - Optional custom content generation
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell to render.
     * @param DataContext $context Data rendering context.
     *
     * @throws InvalidArgumentException If the column is not a CheckboxColumn.
     * @return Cell The rendered data cell.
     */
    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $this->checkColumn($column);

        $inputAttributes = $column->inputAttributes;
        $name = null;
        $value = null;

        if (!array_key_exists('name', $inputAttributes)) {
            $name = 'checkbox-selection';
        }

        if (!array_key_exists('value', $inputAttributes)) {
            $value = $context->key;
        }

        $input = Html::checkbox($name, $value, $inputAttributes);

        $contentClosure = $column->content;
        /** @var string|Stringable $content */
        $content = $contentClosure === null ? $input : $contentClosure($input, $context);

        return $cell
            ->addAttributes($column->bodyAttributes)
            ->content($content)
            ->encode(false);
    }

    /**
     * Render the column footer.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell to render.
     * @param GlobalContext $context Global rendering context.
     *
     * @throws InvalidArgumentException If the column is not a CheckboxColumn.
     * @return Cell The rendered footer cell.
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
     * Verify that the column is a CheckboxColumn.
     *
     * @param ColumnInterface $column The column to check.
     *
     * @throws InvalidArgumentException If the column is not a CheckboxColumn.
     *
     * @psalm-assert CheckboxColumn $column
     */
    private function checkColumn(ColumnInterface $column): void
    {
        if (!$column instanceof CheckboxColumn) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s", but "%s" given.',
                    CheckboxColumn::class,
                    $column::class
                )
            );
        }
    }
}
