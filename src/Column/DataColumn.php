<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Yiisoft\Yii\DataView\Filter\Filter;

/**
 * DetailColumn is the default column type for the {@see GridView} widget.
 *
 * A simple data column definition refers to an attribute in the data of the GridView's data provider.
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
    public readonly ?string $queryProperty;

    public function __construct(
        public readonly ?string $property = null,
        ?string $queryProperty = null,
        public readonly ?string $header = null,
        public readonly bool $encodeHeader = true,
        public readonly ?string $footer = null,
        public readonly array $columnAttributes = [],
        public readonly array $headerAttributes = [],
        public readonly array $bodyAttributes = [],
        public readonly bool $withSorting = true,
        public readonly mixed $content = null,
        public readonly ?string $dateTimeFormat = null,
        public readonly bool|Filter $filter = false,
        private readonly bool $visible = true,
    ) {
        $this->queryProperty = $queryProperty ?? $this->property;
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
