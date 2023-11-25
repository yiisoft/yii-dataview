<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use InvalidArgumentException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Html\Tag\Input;
use Yiisoft\Html\Tag\Select;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Helper\Attribute;
use Yiisoft\Yii\DataView\LinkSorter;

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

    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);

        $label = $column->header ?? ($column->property === null ? '' : ucfirst($column->property));

        if ($column->property !== null && $column->withSorting) {
            $linkSorter = $this->renderLinkSorter($context, $column->property, $label);
            if (!empty($linkSorter)) {
                return $cell->content($linkSorter)->encode(false);
            }
        }

        return $cell
            ->addAttributes($column->headerAttributes)
            ->content($label);
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
            $content = (string)(is_callable($contentSource) ? $contentSource($context) : $contentSource);
        } elseif ($column->property !== null) {
            $content = (string)ArrayHelper::getValue($context->data, $column->property);
        } else {
            $content = '';
        }

        return $cell
            ->addAttributes($column->bodyAttributes)
            ->content($content);
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);

        if ($column->footer !== null) {
            $cell = $cell->content($column->footer);
        }

        return $cell;
    }

    private function renderLinkSorter(GlobalContext $context, string $property, string $label): string
    {
        $dataReader = $context->dataReader;
        if (!$dataReader instanceof PaginatorInterface) {
            return '';
        }

        $sort = $dataReader->getSort();
        if ($sort === null) {
            return '';
        }

        $linkSorter = $dataReader instanceof OffsetPaginator
            ? LinkSorter::widget()->currentPage($dataReader->getCurrentPage())
            : LinkSorter::widget();

        return $linkSorter
            ->attribute($property)
            ->attributes($sort->getCriteria())
            ->directions($sort->getOrder())
            ->iconAscClass('bi bi-sort-alpha-up')
            ->iconDescClass('bi bi-sort-alpha-down')
            ->label($label)
            ->linkAttributes($context->sortLinkAttributes)
            ->pageSize($dataReader->getPageSize())
            ->urlArguments($context->urlArguments)
            ->urlQueryParameters($context->urlQueryParameters)
            ->render();
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
