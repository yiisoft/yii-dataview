<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use DateTimeInterface;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\FilterContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;
use Yiisoft\Yii\DataView\Filter\Factory\EqualsFilterFactory;
use Yiisoft\Yii\DataView\Filter\Factory\FilterFactoryInterface;
use Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory;
use Yiisoft\Yii\DataView\Filter\Widget\Context;
use Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter;
use Yiisoft\Yii\DataView\Filter\Widget\TextInputFilter;
use Yiisoft\Yii\DataView\UrlQueryReader;

final class DataColumnRenderer implements FilterableColumnRendererInterface
{
    public function __construct(
        private readonly ContainerInterface $filterFactoryContainer,
        private readonly string $dateTimeFormat = 'Y-m-d H:i:s',
        private readonly string $defaultFilterFactory = LikeFilterFactory::class,
        private readonly string $defaultArrayFilterFactory = EqualsFilterFactory::class,
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
            $label = $column->property === null ? '' : Html::encode(ucfirst($column->property));
        } else {
            $label = $column->encodeHeader ? Html::encode($column->header) : $column->header;
        }
        $cell = $cell->content($label);

        if (!$column->withSorting || $column->queryProperty === null) {
            return $cell;
        }

        [$cell, $link, $prepend, $append] = $context->prepareSortable($cell, $column->queryProperty);
        if ($link !== null) {
            $link = $link->content($label)->encode(false);
        }

        return $cell->content($prepend . ($link ?? $label) . $append);
    }

    public function renderFilter(ColumnInterface $column, Cell $cell, FilterContext $context): ?Cell
    {
        $this->checkColumn($column);

        if ($column->queryProperty === null || $column->filter === false) {
            return null;
        }

        if ($column->filter === true) {
            $widget = TextInputFilter::widget();
        } elseif (is_array($column->filter)) {
            $widget = DropdownFilter::widget()->optionsData($column->filter);
        } else {
            $widget = $column->filter;
        }

        $widget = $widget->withContext(
            new Context(
                $column->queryProperty,
                $context->getQueryValue($column->queryProperty),
                $context->formId
            )
        );

        return $cell->content($widget)->encode(false);
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

    public function makeFilter(ColumnInterface $column, UrlQueryReader $urlQueryReader): ?FilterInterface
    {
        $this->checkColumn($column);
        if ($column->queryProperty === null) {
            return null;
        }

        $value = $urlQueryReader->get($column->queryProperty);
        if ($value === null || $value === '') {
            return null;
        }

        if ($column->filterFactory === null) {
            /** @var FilterFactoryInterface $factory */
            $factory = $this->filterFactoryContainer->get(
                is_array($column->filter) ? $this->defaultArrayFilterFactory : $this->defaultFilterFactory
            );
        } elseif (is_string($column->filterFactory)) {
            /** @var FilterFactoryInterface $factory */
            $factory = $this->filterFactoryContainer->get($column->filterFactory);
        } else {
            $factory = $column->filterFactory;
        }

        return $factory->create($column->queryProperty, $value);
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
