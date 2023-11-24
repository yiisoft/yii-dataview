<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Stringable;
use Yiisoft\Yii\DataView\Helper\Attribute;

/**
 * DetailColumn is the default column type for the {@see GridView} widget.
 *
 * It is used to show data columns and allows {@see withSorting|sorting} and {@see filter|filtering} them.
 *
 * A simple data column definition refers to an attribute in the data of the GridView's data provider.
 *
 * The name of the attribute is specified by {@see attribute}.
 *
 * By setting {@see value} and {@see label}, the label and cell content can be customized.
 *
 * A data column differentiates between the {@see getDataCellValue|data cell value} and the
 * {@see renderDataCellContent|data cell content}. The cell value is an un-formatted value that may be used for
 * calculation, while the actual cell content is a {@see format|formatted} version of that value which may contain HTML
 * markup.
 */
final class DataColumn implements ColumnInterface
{
    /**
     * @psalm-param array<array-key, string|array<array-key,string>> $filterInputSelectItems
     */
    public function __construct(
        public readonly ?string $property = null,
        public readonly ?string $header = null,
        public readonly ?string $footer = null,
        public readonly array $columnAttributes = [],
        public readonly array $headerAttributes = [],
        public readonly array $filterAttributes = [],
        public readonly array $bodyAttributes = [],
        public readonly bool $withSorting = true,
        public readonly mixed $content = null,
        public readonly ?string $filter = null,
        public readonly ?string $filterProperty = null,
        public readonly string $filterType = 'text',
        public readonly array $filterInputAttributes = [],
        public readonly ?string $filterModelName = null,
        public readonly Stringable|null|string|int|bool|float $filterValueDefault = null,
        public readonly array $filterInputSelectItems = [],
        public readonly string $filterInputSelectPrompt = '',
        private readonly bool $visible = true,
    ) {
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
