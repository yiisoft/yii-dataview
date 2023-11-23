<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use InvalidArgumentException;
use Stringable;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;

final class RadioColumnRenderer implements ColumnRendererInterface
{
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->addAttributes($column->getColumnAttributes());
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): ?Cell
    {
        $this->checkColumn($column);

        $header = $column->getHeader();
        if ($header === null) {
            return null;
        }

        return $cell
            ->addAttributes($column->getHeaderAttributes())
            ->content($header);
    }

    public function renderFilter(ColumnInterface $column, Cell $cell, GlobalContext $context): ?Cell
    {
        return null;
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $this->checkColumn($column);

        $inputAttributes = $column->getInputAttributes();
        $name = null;
        $value = null;

        if (!array_key_exists('name', $inputAttributes)) {
            $name = 'radio-selection';
        }

        if (!array_key_exists('value', $inputAttributes)) {
            $key = $context->getKey();
            $value = is_array($key)
                ? json_encode($key, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : (string)$key;
        }

        $input = Html::radio($name, $value, $inputAttributes);

        $contentClosure = $column->getContent();
        /** @var string|Stringable $content */
        $content = $contentClosure === null ? $input : $contentClosure($input, $context);

        return $cell
            ->addAttributes($column->getBodyAttributes())
            ->content($content)
            ->encode(false);
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);

        if ($column->getFooter() !== null) {
            $cell = $cell->content($column->getFooter());
        }

        return $cell;
    }

    /**
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
