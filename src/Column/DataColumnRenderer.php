<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use InvalidArgumentException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Input;
use Yiisoft\Html\Tag\Select;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;
use Yiisoft\Yii\DataView\Helper\Attribute;

final class DataColumnRenderer implements ColumnRendererInterface
{
    private const FILTER_TYPES = [
        'date' => 'date',
        'datetime' => 'datetime-local',
        'email' => 'email',
        'month' => 'month',
        'number' => 'number',
        'range' => 'range',
        'search' => 'search',
        'select' => 'select',
        'tel' => 'tel',
        'text' => 'text',
        'time' => 'time',
        'url' => 'url',
        'week' => 'week',
    ];

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
            $label = $column->property === null ? '' : Html::encode(ucfirst($column->property));
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

    public function renderFilter(ColumnInterface $column, Cell $cell, GlobalContext $context): ?Cell
    {
        $this->checkColumn($column);

        if (!isset(self::FILTER_TYPES[$column->filterType])) {
            throw new InvalidArgumentException(sprintf('Invalid filter type "%s".', $column->filterType));
        }

        if ($column->filter !== null) {
            $content = $column->filter;
        } elseif ($column->filterProperty !== null) {
            $content = match (self::FILTER_TYPES[$column->filterType]) {
                'select' => $this->renderFilterSelect($column, $context),
                default => $this->renderFilterInput($column, $context),
            };
        } else {
            return null;
        }

        return $cell
            ->content($content)
            ->addAttributes($column->filterAttributes)
            ->encode(false);
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $this->checkColumn($column);

        $contentSource = $column->content;

        if ($contentSource !== null) {
            $content = (string)(is_callable($contentSource) ? $contentSource($context->data, $context) : $contentSource);
        } elseif ($column->property !== null) {
            $content = Html::encode((string)ArrayHelper::getValue($context->data, $column->property));
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

    private function renderFilterInput(DataColumn $column, GlobalContext $context): string
    {
        $filterInputAttributes = $column->filterInputAttributes;
        $filterInputTag = Input::tag();

        if (!array_key_exists('name', $filterInputAttributes)) {
            $filterInputTag = $filterInputTag->name(
                Attribute::getInputName(
                    (string)($column->filterModelName ?? $context->filterModelName),
                    $column->filterProperty ?? ''
                ),
            );
        }

        if (!array_key_exists('value', $filterInputAttributes) && $column->filterValueDefault !== '') {
            $filterInputTag = $filterInputTag->value($column->filterValueDefault);
        }

        return $filterInputTag
            ->addAttributes($filterInputAttributes)
            ->type(self::FILTER_TYPES[$column->filterType])
            ->render();
    }

    private function renderFilterSelect(DataColumn $column, GlobalContext $context): string
    {
        $filterInputAttributes = $column->filterInputAttributes;
        $filterSelectTag = Select::tag();

        if (!array_key_exists('name', $filterInputAttributes)) {
            $filterSelectTag = $filterSelectTag->name(
                Attribute::getInputName(
                    (string)($column->filterModelName ?? $context->filterModelName),
                    $column->filterProperty ?? ''
                ),
            );
        }

        if ($column->filterValueDefault !== null) {
            $filterSelectTag = $filterSelectTag->value($column->filterValueDefault);
        }

        return $filterSelectTag
            ->addAttributes($filterInputAttributes)
            ->optionsData($column->filterInputSelectItems)
            ->prompt($column->filterInputSelectPrompt)
            ->render();
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
