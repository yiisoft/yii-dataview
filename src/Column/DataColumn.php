<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use InvalidArgumentException;
use Stringable;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Html\Tag\Input;
use Yiisoft\Html\Tag\Select;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Helper\Attribute;

use Yiisoft\Yii\DataView\LinkSorter;

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
final class DataColumn implements ColumnInterface, ColumnRendererInterface
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

    private string $linkSorter = '';
    private mixed $value = null;

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
        return $this->filterType;
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

    /**
     * Return new instance with the filter input select items.
     *
     * @param array $values The select items for the filter input.
     *
     * This property is used in combination with the {@see filter} property. When {@see filter} is not set or is an
     * array, this property will be used to render the HTML attributes for the generated filter input fields.
     *
     * @psalm-param string[] $values
     */
    public function filterInputSelectItems(array $values): self
    {
        $new = clone $this;
        $new->filterInputSelectItems = $values;

        return $new;
    }

    /**
     * Return new instance with the filter type.
     *
     * @param string $value The filter type.
     */
    public function filterType(string $value): self
    {
        if (!isset($this->filterTypes[$value])) {
            throw new InvalidArgumentException(sprintf('Invalid filter type "%s".', $value));
        }

        $new = clone $this;
        $new->filterType = $value;

        return $new;
    }

    /**
     * Return new instance with the link sorter.
     *
     * @param string $value The URL that will be used to sort the data in this column.
     */
    public function linkSorter(string $value): self
    {
        $new = clone $this;
        $new->linkSorter = $value;

        return $new;
    }

    /**
     * Return new instance with the value of column.
     *
     * @param mixed $value An anonymous function or a string that is used to determine the value to
     * display in the current column.
     *
     * If this is an anonymous function, it will be called for each row and the return value will be used as the value
     * to display for every data. The signature of this function should be:
     *
     * `function ($data, $key, $index, $column)`.
     *
     * Where `$data`, `$key`, and `$index` refer to the data, key and index of the row currently being rendered
     * and `$column` is a reference to the {@see DetailColumn} object.
     *
     * You may also set this property to a string representing the attribute name to be displayed in this column.
     *
     * This can be used when the attribute to be displayed is different from the {@see attribute} that is used for
     * sorting and filtering.
     *
     * If this is not set, `$data[$attribute]` will be used to obtain the value, where `$attribute` is the value of
     * {@see attribute}.
     */
    public function value(mixed $value): self
    {
        $new = clone $this;
        $new->value = $value;

        return $new;
    }

    /**
     * Renders the data cell content.
     *
     * @param array|object $data The data.
     * @param mixed $key The key associated with the data.
     * @param int $index The zero-based index of the data in the data provider.
     */
    protected function renderDataCellContent(object|array $data, mixed $key, int $index): string
    {
        if ($this->getContent() !== null) {
            return '';
            //   return parent::renderDataCellContent($data, $key, $index);
        }

        return $this->getDataCellValue($data, $key, $index);
    }

    private function renderFilterInput(self $column, GlobalContext $context): string
    {
        $filterInputAttributes = $column->getFilterInputAttributes();
        $filterInputTag = Input::tag();

        if (!array_key_exists('name', $filterInputAttributes)) {
            $filterInputTag = $filterInputTag->name(
                Attribute::getInputName(
                    (string)($column->getFilterModelName() ?? $context->getFilterModelName()),
                    $column->getFilterProperty()
                ),
            );
        }

        if (!array_key_exists('value', $filterInputAttributes) && $column->getFilterValueDefault() !== '') {
            $filterInputTag = $filterInputTag->value($column->getFilterValueDefault());
        }

        return $filterInputTag
            ->addAttributes($filterInputAttributes)
            ->type($this->filterTypes[$this->filterType])
            ->render();
    }

    private function renderFilterSelect(self $column, GlobalContext $context): string
    {
        $filterInputAttributes = $column->getFilterInputAttributes();
        $filterSelectTag = Select::tag();

        if (!array_key_exists('name', $filterInputAttributes)) {
            $filterSelectTag = $filterSelectTag->name(
                Attribute::getInputName(
                    (string)($column->getFilterModelName() ?? $context->getFilterModelName()),
                    $column->getFilterProperty()
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

    public function getRenderer(): self
    {
        return $this;
    }

    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->addAttributes($column->getColumnAttributes());
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);

        $label = $this->getHeader() ?? ucfirst($this->property);

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
        } elseif ($this->property !== null) {
            $content = (string)ArrayHelper::getValue($context->getData(), $this->property);
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

        if ($this->getFooter() !== null) {
            $cell = $cell->content($this->getFooter());
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

    /**
     * @psalm-assert self $column
     */
    private function checkColumn(ColumnInterface $column): void
    {
        if (!$column instanceof self) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s", but "%s" given.',
                    self::class,
                    $column::class
                )
            );
        }
    }
}
