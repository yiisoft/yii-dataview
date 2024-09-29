<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Yiisoft\Validator\RuleInterface;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Filter\Factory\FilterFactoryInterface;
use Yiisoft\Yii\DataView\Filter\Widget\FilterWidget;

/**
 * DetailColumn is the default column type for the {@see GridView} widget.
 *
 * A simple data column definition refers to an attribute in the data of the GridView's data provider.
 *
 * By setting {@see value} and {@see label}, the label and cell content can be customized.
 *
 * A data column differentiates between the {@see getDataCellValue|data cell value} and the
 * {@see renderDataCellContent|data cell content}. The cell value is an unformatted value that may be used for
 * calculation, while the actual cell content is a {@see format|formatted} version of that value which may contain HTML
 * markup.
 *
 * @psalm-type FilterEmptyCallable = callable(mixed $value): bool
 * @psalm-type BodyAttributesCallable = callable(array|object,DataContext): array
 */
final class DataColumn implements ColumnInterface
{
    /**
     * @var bool|callable|null
     * @psalm-var bool|FilterEmptyCallable|null
     */
    public readonly mixed $filterEmpty;

    /**
     * @param array|callable $bodyAttributes
     *
     * @psalm-param array|BodyAttributesCallable $bodyAttributes
     * @psalm-param bool|array<array-key,string|array<array-key,string>>|FilterWidget $filter
     * @psalm-param RuleInterface[]|RuleInterface|null $filterValidation
     * @psalm-param bool|FilterEmptyCallable|null $filterEmpty
     */
    public function __construct(
        public readonly ?string $property = null,
        public readonly ?string $field = null,
        public readonly ?string $header = null,
        public readonly bool $encodeHeader = true,
        public readonly ?string $footer = null,
        public readonly array $columnAttributes = [],
        public readonly array $headerAttributes = [],
        public readonly mixed $bodyAttributes = [],
        public readonly bool $withSorting = true,
        public readonly mixed $content = null,
        public readonly ?string $dateTimeFormat = null,
        public readonly bool|array|FilterWidget $filter = false,
        public readonly string|FilterFactoryInterface|null $filterFactory = null,
        public readonly array|RuleInterface|null $filterValidation = null,
        bool|callable|null $filterEmpty = null,
        private readonly bool $visible = true,
        public readonly ?string $columnClass = null,
        public readonly ?string $headerClass = null,
        public readonly ?string $bodyClass = null,
    ) {
        $this->filterEmpty = $filterEmpty;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function getRenderer(): string
    {
        return DataColumnRenderer::class;
    }
}
