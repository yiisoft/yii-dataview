<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\GridView\Column;

use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\GridView\Column\Base\Cell;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\Base\GlobalContext;

use function array_key_exists;

/**
 * `CheckboxColumnRenderer` renders checkbox inputs in a grid column.
 *
 * @implements ColumnRendererInterface<CheckboxColumn>
 */
final class CheckboxColumnRenderer implements ColumnRendererInterface
{
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        return $cell->addAttributes($column->columnAttributes);
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): ?Cell
    {
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

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
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

        $content = $column->content === null
            ? $input
            : ($column->content)($input, $context);

        return $cell
            ->addAttributes($column->bodyAttributes)
            ->content($content)
            ->encode(false);
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        if ($column->footer !== null) {
            $cell = $cell->content($column->footer);
        }

        return $cell;
    }
}
