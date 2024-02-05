<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use DateTimeInterface;
use InvalidArgumentException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;

final class DataColumnRenderer implements ColumnRendererInterface
{
    public function __construct(
        private readonly string $dateTimeFormat = 'Y-m-d H:i:s',
    ) {
    }

    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->addAttributes($column->columnAttributes);
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): Cell
    {
        $this->checkColumn($column);

        $cell = $cell
            ->addAttributes($column->headerAttributes)
            ->encode(false);

        if ($column->header === null) {
            $label = $column->property === null ? '' : Html::encode(ucfirst((string) $column->property));
        } else {
            $label = $column->encodeHeader ? Html::encode($column->header) : $column->header;
        }
        $cell = $cell->content($label);

        if (!$column->withSorting || $column->property === null) {
            return $cell;
        }

        [$cell, $link, $prepend, $append] = $context->prepareSortable($cell, $column->property);
        if ($link !== null) {
            $link = $link->content($label)->encode(false);
        }

        return $cell->content($prepend . ($link ?? $label) . $append);
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $this->checkColumn($column);

        $contentSource = $column->content;

        if ($contentSource !== null) {
            $content = (string)(is_callable($contentSource) ? $contentSource($context->data, $context) : $contentSource);
        } elseif ($column->property !== null) {
            $value = ArrayHelper::getValue($context->data, $column->property);
            $value = $this->castToString($value, $column);
            $content = Html::encode($value);
        } else {
            $content = '';
        }

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

    private function castToString(mixed $value, DataColumn $column): string
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format($column->dateTimeFormat ?? $this->dateTimeFormat);
        }

        return (string) $value;
    }

    /**
     * @psalm-assert DataColumn $column
     */
    private function checkColumn(ColumnInterface $column): void
    {
        if (!$column instanceof DataColumn) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s", but "%s" given.',
                    DataColumn::class,
                    $column::class
                )
            );
        }
    }
}
