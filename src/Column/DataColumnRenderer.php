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
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->addAttributes($column->getColumnAttributes());
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);

        $label = $column->getHeader() ?? ($column->getProperty() === null ? '' : ucfirst($column->getProperty()));

        if ($column->getProperty() !== null && $column->isWithSorting()) {
            $linkSorter = $this->renderLinkSorter($context, $column->getProperty(), $label);
            if (!empty($linkSorter)) {
                return $cell->content($linkSorter)->encode(false);
            }
        }

        return $cell
            ->addAttributes($column->getHeaderAttributes())
            ->content($label);
    }

    public function renderFilter(ColumnInterface $column, Cell $cell, GlobalContext $context): ?Cell
    {
        $this->checkColumn($column);

        if ($column->getFilter() !== null) {
            $content = $column->getFilter();
        } elseif ($column->getFilterProperty() !== null) {
            $content = match ($column->getFilterType()) {
                'select' => $this->renderFilterSelect($column, $context),
                default => $this->renderFilterInput($column, $context),
            };
        } else {
            return null;
        }

        return $cell
            ->content($content)
            ->addAttributes($column->getFilterAttributes())
            ->encode(false);
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $this->checkColumn($column);

        $contentSource = $column->getContent();

        if ($contentSource !== null) {
            $content = (string)(is_callable($contentSource) ? $contentSource($context) : $contentSource);
        } elseif ($column->getProperty() !== null) {
            $content = (string)ArrayHelper::getValue($context->getData(), $column->getProperty());
        } else {
            $content = '';
        }

        return $cell
            ->addAttributes($column->getBodyAttributes())
            ->content($content);
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);

        if ($column->getFooter() !== null) {
            $cell = $cell->content($column->getFooter());
        }

        return $cell;
    }

    private function renderLinkSorter(GlobalContext $context, string $property, string $label): string
    {
        $dataReader = $context->getDataReader();
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
            ->linkAttributes($context->getSortLinkAttributes())
            ->pageSize($dataReader->getPageSize())
            ->urlArguments($context->getUrlArguments())
            ->urlQueryParameters($context->getUrlQueryParameters())
            ->render();
    }

    private function renderFilterInput(DataColumn $column, GlobalContext $context): string
    {
        $filterInputAttributes = $column->getFilterInputAttributes();
        $filterInputTag = Input::tag();

        if (!array_key_exists('name', $filterInputAttributes)) {
            $filterInputTag = $filterInputTag->name(
                Attribute::getInputName(
                    (string)($column->getFilterModelName() ?? $context->getFilterModelName()),
                    $column->getFilterProperty() ?? ''
                ),
            );
        }

        if (!array_key_exists('value', $filterInputAttributes) && $column->getFilterValueDefault() !== '') {
            $filterInputTag = $filterInputTag->value($column->getFilterValueDefault());
        }

        return $filterInputTag
            ->addAttributes($filterInputAttributes)
            ->type($column->getFilterType())
            ->render();
    }

    private function renderFilterSelect(DataColumn $column, GlobalContext $context): string
    {
        $filterInputAttributes = $column->getFilterInputAttributes();
        $filterSelectTag = Select::tag();

        if (!array_key_exists('name', $filterInputAttributes)) {
            $filterSelectTag = $filterSelectTag->name(
                Attribute::getInputName(
                    (string)($column->getFilterModelName() ?? $context->getFilterModelName()),
                    $column->getFilterProperty() ?? ''
                ),
            );
        }

        if ($column->getFilterValueDefault() !== null) {
            $filterSelectTag = $filterSelectTag->value($column->getFilterValueDefault());
        }

        return $filterSelectTag
            ->addAttributes($filterInputAttributes)
            ->optionsData($column->getFilterInputSelectItems())
            ->prompt($column->getFilterInputSelectPrompt())
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
