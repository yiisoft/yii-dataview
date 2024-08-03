<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use DateTimeInterface;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Html\Html;
use Yiisoft\Validator\EmptyCondition\NeverEmpty;
use Yiisoft\Validator\EmptyCondition\WhenEmpty;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\FilterContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;
use Yiisoft\Yii\DataView\Column\Base\MakeFilterContext;
use Yiisoft\Yii\DataView\Filter\Factory\EqualsFilterFactory;
use Yiisoft\Yii\DataView\Filter\Factory\FilterFactoryInterface;
use Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory;
use Yiisoft\Yii\DataView\Filter\Widget\Context;
use Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter;
use Yiisoft\Yii\DataView\Filter\Widget\TextInputFilter;

use function call_user_func;
use function is_array;
use function is_callable;

/**
 * @psalm-import-type FilterEmptyCallable from DataColumn
 */
final class DataColumnRenderer implements FilterableColumnRendererInterface, OverrideOrderFieldsColumnInterface
{
    /**
     * @var bool|callable
     * @psalm-var bool|FilterEmptyCallable
     */
    private readonly mixed $defaultFilterEmpty;

    /**
     * @psalm-param bool|FilterEmptyCallable $defaultFilterEmpty
     */
    public function __construct(
        private readonly ContainerInterface $filterFactoryContainer,
        private readonly ValidatorInterface $validator,
        private readonly string $dateTimeFormat = 'Y-m-d H:i:s',
        private readonly string $defaultFilterFactory = LikeFilterFactory::class,
        private readonly string $defaultArrayFilterFactory = EqualsFilterFactory::class,
        bool|callable $defaultFilterEmpty = true,
    ) {
        $this->defaultFilterEmpty = $defaultFilterEmpty;
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
        $this->checkColumn($column);

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

        $errors = $context->validationResult->getAttributeErrorMessages($column->property);
        if (!empty($errors)) {
            $cell = $cell->addClass($context->cellInvalidClass);
            $content[] = Html::div(attributes: $context->errorsContainerAttributes)
                ->content(...array_map(static fn(string $error) => Html::div($error), $errors));
        }

        return $cell->content(...$content)->encode(false);
    }

    public function makeFilter(ColumnInterface $column, MakeFilterContext $context): ?FilterInterface
    {
        $this->checkColumn($column);
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

        return $factory->create($column->field ?? $column->property, $value);
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $this->checkColumn($column);
        /** @var DataColumn $column This annotation need for IDE only */

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

        if (is_array($column->bodyAttributes)) {
            $attributes = $column->bodyAttributes;
        } else {
            /** @var array $attributes Remove annotation after fix https://github.com/vimeo/psalm/issues/11062 */
            $attributes = call_user_func(
                $column->bodyAttributes,
                $context->data,
                $context,
            );
        }

        return $cell
            ->addAttributes($attributes)
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

    public function getOverrideOrderFields(ColumnInterface $column): array
    {
        $this->checkColumn($column);

        if ($column->property === null
            || $column->field === null
            || $column->property === $column->field
        ) {
            return [];
        }

        return [$column->property => $column->field];
    }
}
