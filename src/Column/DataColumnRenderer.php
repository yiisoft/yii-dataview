<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use DateTimeInterface;
use Psr\Container\ContainerInterface;
use Stringable;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Html\Html;
use Yiisoft\Html\NoEncodeStringableInterface;
use Yiisoft\Validator\EmptyCondition\NeverEmpty;
use Yiisoft\Validator\EmptyCondition\WhenEmpty;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\FilterContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\MakeFilterContext;
use Yiisoft\Yii\DataView\Filter\Factory\EqualsFilterFactory;
use Yiisoft\Yii\DataView\Filter\Factory\FilterFactoryInterface;
use Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory;
use Yiisoft\Yii\DataView\Filter\Widget\Context;
use Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter;
use Yiisoft\Yii\DataView\Filter\Widget\TextInputFilter;

use function is_array;
use function is_callable;

/**
 * `DataColumnRenderer` handles rendering and filtering of data columns in a grid.
 *
 * @template TColumn as DataColumn
 * @implements FilterableColumnRendererInterface<TColumn>
 * @implements SortableColumnInterface<TColumn>
 *
 * @psalm-import-type FilterEmptyCallable from DataColumn
 */
final class DataColumnRenderer implements FilterableColumnRendererInterface, SortableColumnInterface
{
    /**
     * Default function to determine if a filter value is empty.
     *
     * @var bool|callable
     * @psalm-var bool|FilterEmptyCallable
     */
    private readonly mixed $defaultFilterEmpty;

    /**
     * Creates a new `DataColumnRenderer` instance.
     *
     * @param ContainerInterface $filterFactoryContainer Container for filter factory instances.
     * @param ValidatorInterface $validator Validator for filter values.
     * @param string $dateTimeFormat Default format for datetime values (e.g., 'Y-m-d H:i:s').
     * @param FilterFactoryInterface|string $defaultFilterFactory Default filter factory for non-array filters.
     * @param FilterFactoryInterface|string $defaultArrayFilterFactory Default filter factory for array filters.
     * @param bool|callable $defaultFilterEmpty Default function to determine if a filter value is empty.
     *
     * @psalm-param bool|FilterEmptyCallable $defaultFilterEmpty
     */
    public function __construct(
        private readonly ContainerInterface $filterFactoryContainer,
        private readonly ValidatorInterface $validator,
        private readonly string $dateTimeFormat = 'Y-m-d H:i:s',
        private readonly string|FilterFactoryInterface $defaultFilterFactory = LikeFilterFactory::class,
        private readonly string|FilterFactoryInterface $defaultArrayFilterFactory = EqualsFilterFactory::class,
        bool|callable $defaultFilterEmpty = true,
    ) {
        $this->defaultFilterEmpty = $defaultFilterEmpty;
    }

    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        /** @var DataColumn $column This annotation is for IDE only */

        return $cell
            ->addAttributes($column->columnAttributes)
            ->addClass($column->columnClass);
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        /** @var DataColumn $column This annotation is for IDE only */

        $cell = $cell
            ->addAttributes($column->headerAttributes)
            ->addClass($column->headerClass)
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

    public function renderFilter(ColumnInterface $column, Cell $cell, FilterContext $context): ?Cell
    {
        /** @var DataColumn $column This annotation is for IDE only */

        if ($column->property === null || $column->filter === false) {
            return null;
        }

        if ($column->filter === true) {
            $widget = TextInputFilter::widget();
        } elseif (is_array($column->filter)) {
            $widget = DropdownFilter::widget()->optionsData($column->filter);
        } else {
            $widget = $column->filter;
        }

        $content = [
            $widget->withContext(
                new Context(
                    $column->property,
                    $context->getQueryValue($column->property),
                    $context->formId
                )
            ),
        ];

        $errors = $context->validationResult->getPropertyErrorMessages($column->property);
        if (!empty($errors)) {
            $cell = $cell->addClass($context->cellInvalidClass);
            $content[] = Html::div(attributes: $context->errorsContainerAttributes)
                ->content(...array_map(static fn(string $error) => Html::div($error), $errors));
        }

        return $cell->content(...$content)->encode(false);
    }

    public function makeFilter(ColumnInterface $column, MakeFilterContext $context): ?FilterInterface
    {
        /** @var DataColumn $column This annotation is for IDE only */

        if ($column->property === null) {
            return null;
        }

        $value = $context->getQueryValue($column->property);
        if ($value === null) {
            return null;
        }

        $filterEmpty = $this->normalizeFilterEmpty($column->filterEmpty ?? $this->defaultFilterEmpty);
        if ($filterEmpty($value)) {
            return null;
        }

        if ($column->filterValidation !== null) {
            $result = $this->validator->validate($value, $column->filterValidation);
            if (!$result->isValid()) {
                foreach ($result->getErrors() as $error) {
                    $context->validationResult->addError(
                        $error->getMessage(),
                        $error->getParameters(),
                        [$column->property]
                    );
                }
                return null;
            }
        }

        return $this
            ->getFilterFactory($column)
            ->create($column->field ?? $column->property, $value);
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        /** @var DataColumn $column This annotation is for IDE only */

        if ($column->content === null) {
            $content = $this->extractValueFromData($column, $context);
        } elseif (is_callable($column->content)) {
            $content = ($column->content)($context->data, $context);
        } else {
            $content = $column->content;
        }
        $content = $this->encodeContent($content, $column->encodeContent);

        if (is_callable($column->bodyAttributes)) {
            /** @var array $attributes Remove annotation after fix https://github.com/vimeo/psalm/issues/11062 */
            $attributes = ($column->bodyAttributes)(
                $context->data,
                $context,
            );
        } else {
            $attributes = $column->bodyAttributes;
        }

        return $cell
            ->addAttributes($attributes)
            ->addClass($column->bodyClass)
            ->content($content)
            ->encode(false);
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        /** @var DataColumn $column This annotation is for IDE only */

        if ($column->footer !== null) {
            $cell = $cell->content($column->footer);
        }

        return $cell;
    }

    /**
     * Normalize a filter empty value to a callable.
     *
     * @param bool|callable $value The value to normalize.
     *
     * @return callable The normalized callable.
     *
     * @psalm-param bool|FilterEmptyCallable $value
     * @psalm-return FilterEmptyCallable
     */
    private function normalizeFilterEmpty(bool|callable $value): callable
    {
        if ($value === false) {
            return new NeverEmpty();
        }

        if ($value === true) {
            return new WhenEmpty();
        }

        return $value;
    }

    public function getOrderProperties(ColumnInterface $column): array
    {
        /** @var DataColumn $column This annotation is for IDE only */

        if ($column->property === null) {
            return [];
        }

        return [$column->property => $column->field ?? $column->property];
    }

    private function getFilterFactory(DataColumn $column): FilterFactoryInterface
    {
        if ($column->filterFactory === null) {
            $factory = is_array($column->filter)
                ? $this->defaultArrayFilterFactory
                : $this->defaultFilterFactory;
        } else {
            $factory = $column->filterFactory;
        }

        if ($factory instanceof FilterFactoryInterface) {
            return $factory;
        }

        /** @var FilterFactoryInterface */
        return $this->filterFactoryContainer->get($factory);
    }

    private function extractValueFromData(DataColumn $column, DataContext $context): string|Stringable
    {
        if ($column->property === null) {
            return '';
        }

        $value = ArrayHelper::getValue($context->data, $column->property);

        if ($value === null) {
            return '';
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format($column->dateTimeFormat ?? $this->dateTimeFormat);
        }

        return $value instanceof Stringable ? $value : (string) $value;
    }

    private function encodeContent(string|Stringable|int|float $content, ?bool $encode): string
    {
        $encode ??= !$content instanceof NoEncodeStringableInterface;

        $contentAsString = (string) $content;

        return $encode ? Html::encode($contentAsString) : $contentAsString;
    }
}
