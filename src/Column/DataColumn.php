<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use InvalidArgumentException;
use Stringable;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Helper\Attribute;

use function sprintf;

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
    /** @psalm-var string[] */
    private array $filterTypes = [
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

    /**
     * @psalm-param array<array-key, string|array<array-key,string>> $filterInputSelectItems
     */
    public function __construct(
        private ?string $property = null,
        private ?string $header = null,
        private ?string $footer = null,
        private array $columnAttributes = [],
        private array $headerAttributes = [],
        private array $filterAttributes = [],
        private array $bodyAttributes = [],
        private bool $withSorting = true,
        private mixed $content = null,
        private ?string $filter = null,
        private ?string $filterProperty = null,
        private string $filterType = 'text',
        private array $filterInputAttributes = [],
        private ?string $filterModelName = null,
        private Stringable|null|string|int|bool|float $filterValueDefault = null,
        private array $filterInputSelectItems = [],
        private string $filterInputSelectPrompt = '',
        private bool $visible = true,
    ) {
        if (!isset($this->filterTypes[$filterType])) {
            throw new InvalidArgumentException(sprintf('Invalid filter type "%s".', $filterType));
        }
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function getFooter(): ?string
    {
        return $this->footer;
    }

    public function getColumnAttributes(): array
    {
        return $this->columnAttributes;
    }

    public function getHeaderAttributes(): array
    {
        return $this->headerAttributes;
    }

    public function getFilterAttributes(): array
    {
        return $this->filterAttributes;
    }

    public function getBodyAttributes(): array
    {
        return $this->bodyAttributes;
    }

    public function isWithSorting(): bool
    {
        return $this->withSorting;
    }

    public function getContent(): mixed
    {
        return $this->content;
    }

    public function getFilter(): ?string
    {
        return $this->filter;
    }

    public function getFilterProperty(): ?string
    {
        return $this->filterProperty;
    }

    public function getFilterType(): string
    {
        return $this->filterTypes[$this->filterType];
    }

    public function getFilterInputAttributes(): array
    {
        return $this->filterInputAttributes;
    }

    public function getFilterModelName(): ?string
    {
        return $this->filterModelName;
    }

    public function getFilterValueDefault(): float|Stringable|bool|int|string|null
    {
        return $this->filterValueDefault;
    }

    /**
     * @psalm-return array<array-key, string|array<array-key,string>>
     */
    public function getFilterInputSelectItems(): array
    {
        return $this->filterInputSelectItems;
    }

    public function getFilterInputSelectPrompt(): string
    {
        return $this->filterInputSelectPrompt;
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
