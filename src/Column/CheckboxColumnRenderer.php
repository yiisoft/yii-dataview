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

final class CheckboxColumnRenderer implements ColumnRendererInterface
{
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->addAttributes($column->columnAttributes);
    }

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

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);

        if ($column->footer !== null) {
            $cell = $cell->content($column->footer);
        }

        return $cell;
    }

    /**
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
