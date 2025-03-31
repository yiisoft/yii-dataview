<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Stringable;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;

use function array_key_exists;

/**
 * `RadioColumnRenderer` handles the rendering of radio button columns in a grid.
 *
 * @implements ColumnRendererInterface<RadioColumn>
 */
final class RadioColumnRenderer implements ColumnRendererInterface
{
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        return $cell->addAttributes($column->columnAttributes);
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): ?Cell
    {
        $header = $column->header;
        if ($header === null) {
            return null;
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

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        if ($column->footer !== null) {
            $cell = $cell->content($column->footer);
        }

        return $cell;
    }
}
