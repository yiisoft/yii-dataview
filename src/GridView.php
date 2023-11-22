<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Closure;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Col;
use Yiisoft\Html\Tag\Colgroup;
use Yiisoft\Html\Tag\Td;
use Yiisoft\Html\Tag\Tr;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\Column\AbstractColumn;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;

/**
 * The GridView widget is used to display data in a grid.
 *
 * It provides features like {@see sorter|sorting}, and {@see filterModel|filtering} the data.
 *
 * The columns of the grid table are configured in terms of {@see Column} classes, which are configured via
 * {@see columns}.
 *
 * The look and feel of a grid view can be customized using the large amount of properties.
 */
final class GridView extends BaseListView
{
    public const FILTER_POS_HEADER = 'header';
    public const FILTER_POS_FOOTER = 'footer';
    public const FILTER_POS_BODY = 'body';
    private Closure|null $afterRow = null;
    private Closure|null $beforeRow = null;
    /** @psalm-var array<array-key,AbstractColumn|null> */
    private array $columns = [];
    private bool $columnsGroupEnabled = false;
    private string $emptyCell = '&nbsp;';
    private string $filterModelName = '';
    private string $filterPosition = self::FILTER_POS_BODY;
    private array $filterRowAttributes = [];
    private bool $footerEnabled = false;
    private array $footerRowAttributes = [];
    private bool $headerTableEnabled = true;
    private array $headerRowAttributes = [];
    private array $rowAttributes = [];
    private array $tableAttributes = [];
    private array $tbodyAttributes = [];

    public function __construct(
        private CurrentRoute $currentRoute,
        TranslatorInterface|null $translator = null,
        UrlGeneratorInterface|null $urlGenerator = null
    ) {
        parent::__construct($translator, $urlGenerator);
    }

    /**
     * Returns a new instance with anonymous function that is called once AFTER rendering each data.
     *
     * @param Closure|null $value The anonymous function that is called once AFTER rendering each data.
     */
    public function afterRow(Closure|null $value): self
    {
        $new = clone $this;
        $new->afterRow = $value;

        return $new;
    }

    /**
     * Return a new instance with anonymous function that is called once BEFORE rendering each data.
     *
     * @param Closure|null $value The anonymous function that is called once BEFORE rendering each data.
     */
    public function beforeRow(Closure|null $value): self
    {
        $new = clone $this;
        $new->beforeRow = $value;

        return $new;
    }

    /**
     * Return a new instance the specified columns.
     *
     * @param AbstractColumn ...$values The grid column configuration. Each array element represents the configuration
     * for one particular grid column. For example,
     *
     * ```php
     * [
     *     SerialColumn::create(),
     *     DetailColumn::create()
     *         ->attribute('identity_id')
     *         ->filterAttribute('identity_id')
     *         ->filterValueDefault(0)
     *         ->filterAttributes(['class' => 'text-center', 'maxlength' => '5', 'style' => 'width:60px']),
     *     ActionColumn::create()->primaryKey('identity_id')->visibleButtons(['view' => true]),
     * ]
     * ```
     */
    public function columns(AbstractColumn ...$values): self
    {
        $new = clone $this;
        $new->columns = $values;

        return $new;
    }

    /**
     * Returns a new instance with the specified column group enabled.
     *
     * @param bool $value Whether to enable the column group.
     */
    public function columnsGroupEnabled(bool $value): self
    {
        $new = clone $this;
        $new->columnsGroupEnabled = $value;

        return $new;
    }

    /**
     * Return new instance with the HTML display when the content is empty.
     *
     * @param string $value The HTML display when the content of a cell is empty. This property is used to render cells
     * that have no defined content, e.g. empty footer or filter cells.
     */
    public function emptyCell(string $value): self
    {
        $new = clone $this;
        $new->emptyCell = $value;

        return $new;
    }

    /**
     * Return new instance with the filter model name.
     *
     * @param string $value The form model name that keeps the user-entered filter data. When this property is set, the
     * grid view will enable column-based filtering. Each data column by default will display a text field at the top
     * that users can fill in to filter the data.
     *
     * Note that in order to show an input field for filtering, a column must have its {@see DataColumn::attribute}
     * property set and the attribute should be active in the current scenario of $filterModelName or have
     * {@see DataColumn::filter} set as the HTML code for the input field.
     */
    public function filterModelName(string $value): self
    {
        $new = clone $this;
        $new->filterModelName = $value;

        return $new;
    }

    /**
     * Return new instance with the filter position.
     *
     * @param string $filterPosition Whether the filters should be displayed in the grid view. Valid values include:
     *
     * - {@see FILTER_POS_HEADER}: The filters will be displayed on top of each column's header cell.
     * - {@see FILTER_POS_BODY}: The filters will be displayed right below each column's header cell.
     * - {@see FILTER_POS_FOOTER}: The filters will be displayed below each column's footer cell.
     */
    public function filterPosition(string $filterPosition): self
    {
        $new = clone $this;
        $new->filterPosition = $filterPosition;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for filter row.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function filterRowAttributes(array $values): self
    {
        $new = clone $this;
        $new->filterRowAttributes = $values;

        return $new;
    }

    /**
     * Return new instance whether to show the footer section of the grid.
     *
     * @param bool $value Whether to show the footer section of the grid.
     */
    public function footerEnabled(bool $value): self
    {
        $new = clone $this;
        $new->footerEnabled = $value;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for footer row.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function footerRowAttributes(array $values): self
    {
        $new = clone $this;
        $new->footerRowAttributes = $values;

        return $new;
    }

    /**
     * Return new instance whether to show the header table section of the grid.
     *
     * @param bool $value Whether to show the header table section of the grid.
     */
    public function headerTableEnabled(bool $value): self
    {
        $new = clone $this;
        $new->headerTableEnabled = $value;

        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the header row.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function headerRowAttributes(array $values): self
    {
        $new = clone $this;
        $new->headerRowAttributes = $values;

        return $new;
    }

    /**
     * Return new instance with the HTML attributes for row of the grid.
     *
     * @param array $values Attribute values indexed by attribute names.
     *
     * This can be either an array specifying the common HTML attributes for all body rows.
     */
    public function rowAttributes(array $values): self
    {
        $new = clone $this;
        $new->rowAttributes = $values;

        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the `table` tag.
     *
     * @param array $attributes The tag attributes in terms of name-value pairs.
     */
    public function tableAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->tableAttributes = $attributes;
        return $new;
    }

    /**
     * Add one or more CSS classes to the `table` tag.
     *
     * @param string|null ...$class One or many CSS classes.
     */
    public function addTableClass(?string ...$class): self
    {
        $new = clone $this;
        Html::addCssClass($new->tableAttributes, $class);
        return $new;
    }

    /**
     * Replace current `table` tag CSS classes with a new set of classes.
     *
     * @param string|null ...$class One or many CSS classes.
     */
    public function tableClass(?string ...$class): static
    {
        $new = clone $this;
        $new->tableAttributes['class'] = array_filter($class, static fn ($c) => $c !== null);
        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the `tbody` tag.
     *
     * @param array $attributes The tag attributes in terms of name-value pairs.
     */
    public function tbodyAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->tbodyAttributes = $attributes;
        return $new;
    }

    /**
     * Add one or more CSS classes to the `tbody` tag.
     *
     * @param string|null ...$class One or many CSS classes.
     */
    public function addTbodyClass(?string ...$class): self
    {
        $new = clone $this;
        Html::addCssClass($new->tbodyAttributes, $class);
        return $new;
    }

    /**
     * Replace current `tbody` tag CSS classes with a new set of classes.
     *
     * @param string|null ...$class One or many CSS classes.
     */
    public function tbodyClass(?string ...$class): static
    {
        $new = clone $this;
        $new->tbodyAttributes['class'] = array_filter($class, static fn ($c) => $c !== null);
        return $new;
    }

    /**
     * Renders the data active record classes for the grid view.
     *
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    protected function renderItems(): string
    {
        $columns = $this->renderColumns();

        $content = array_filter([
            $this->columnsGroupEnabled ? $this->renderColumnGroup($columns) : false,
            $this->headerTableEnabled ? $this->renderTableHeader($columns) : false,
            $this->footerEnabled ? $this->renderTableFooter($columns) : false,
            $this->renderTableBody($columns),
        ]);

        return Html::tag('table', PHP_EOL . implode(PHP_EOL, $content) . PHP_EOL, $this->tableAttributes)
            ->encode(false)
            ->render();
    }

    /**
     * This function tries to guess the columns to show from the given data if {@see columns} are not explicitly
     * specified.
     *
     * @psalm-return list<ActionColumn|DataColumn>|list<array>
     */
    private function guessColumns(): array
    {
        $items = $this->getItems();

        $columns = [];
        foreach ($items as $item) {
            /**
             * @var string $name
             * @var mixed $value
             */
            foreach ($item as $name => $value) {
                if ($value === null || is_scalar($value) || is_callable([$value, '__toString'])) {
                    $columns[] = DataColumn::create()->attribute($name);
                }
            }
            break;
        }

        if (!empty($items)) {
            $columns[] = ActionColumn::create();
        }

        return $columns;
    }

    /**
     * Creates column objects and initializes them.
     *
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     *
     * @psalm-return array<array-key,AbstractColumn>|array
     */
    private function renderColumns(): array
    {
        $columns = $this->columns;

        if ($columns === []) {
            $columns = $this->guessColumns();
        }

        foreach ($columns as $i => $column) {
            if ($column instanceof AbstractColumn && $column->isVisible()) {
                $column = $column->emptyCell($this->emptyCell);

                if ($column instanceof ActionColumn) {
                    $column = $column
                        ->currentRoute($this->currentRoute)
                        ->urlGenerator($this->getUrlGenerator());
                }

                if ($column instanceof DataColumn) {
                    $linkSorter = $this->renderLinkSorter($column->getAttribute(), $column->getLabel());
                    $column = $column->filterModelName($this->filterModelName);

                    if ($linkSorter !== '') {
                        $column = $column->linkSorter($linkSorter);
                    }
                }

                $columns[$i] = $column;
            } else {
                unset($columns[$i]);
            }
        }

        return $columns;
    }

    /**
     * Renders the column group.
     *
     * @param array $columns The columns of gridview.
     *
     * @psalm-param array<array-key,AbstractColumn>|array $columns
     */
    private function renderColumnGroup(array $columns): string
    {
        $cols = [];

        foreach ($columns as $column) {
            if ($column instanceof AbstractColumn) {
                $cols[] = Col::tag()->attributes($column->getAttributes());
            }
        }

        return Colgroup::tag()->columns(...$cols)->render();
    }

    /**
     * Renders the filter.
     *
     * @param array $columns The columns of gridview.
     *
     * @return string The rendering result.
     *
     * @psalm-param array<array-key,AbstractColumn>|array $columns
     */
    private function renderFilters(array $columns): string
    {
        $cells = [];
        $countFilter = 0;
        $filterRowAttributes = $this->filterRowAttributes;

        Html::addCssClass($filterRowAttributes, 'filters');

        foreach ($columns as $column) {
            if ($column instanceof DataColumn && ($column->getFilter() !== '' || $column->getFilterAttribute() !== '')) {
                $cells[] = $column->renderFilterCell();
                $countFilter++;
            } else {
                $cells[] = Td::tag()->content('&nbsp;')->encode(false)->render();
            }
        }

        return match ($countFilter > 0) {
            false => '',
            default => Html::tag('tr', PHP_EOL . implode(PHP_EOL, $cells) . PHP_EOL, $filterRowAttributes)
                ->encode(false)
                ->render(),
        };
    }

    /**
     * Renders the table body.
     *
     * @param array $columns The columns of gridview.
     *
     * @psalm-param array<array-key,AbstractColumn>|array $columns
     *
     * @throws InvalidConfigException
     */
    private function renderTableBody(array $columns): string
    {
        $rows = [];

        $index = 0;
        foreach ($this->getItems() as $key => $value) {
            if ($this->beforeRow !== null) {
                /** @var array */
                $row = call_user_func($this->beforeRow, $value, $key, $index, $this);

                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $rows[] = $this->renderTableRow($columns, $value, $key, $index);

            if ($this->afterRow !== null) {
                /** @psalm-var array<array-key,string> */
                $row = call_user_func($this->afterRow, $value, $key, $index, $this);

                if ($row !== []) {
                    $rows[] = $row;
                }
            }

            $index++;
        }

        if ($rows === [] && $this->emptyText !== '') {
            $colspan = count($columns);

            return Html::tbody($this->tbodyAttributes)
                ->rows(Tr::tag()->cells($this->renderEmpty($colspan)))
                ->render();
        }

        /** @psalm-var array<array-key,string> $rows */
        return Html::tag('tbody', PHP_EOL . implode(PHP_EOL, $rows) . PHP_EOL)->encode(false)->render();
    }

    /**
     * Renders the table footer.
     *
     * @param array $columns The columns of gridview.
     *
     * @psalm-param array<array-key,AbstractColumn>|array $columns
     */
    private function renderTableFooter(array $columns): string
    {
        $cells = [];
        $footerRowAttributes = $this->footerRowAttributes;

        foreach ($columns as $column) {
            if ($column instanceof AbstractColumn) {
                $cells[] = $column->renderFooterCell();
            }
        }

        $content = Html::tag('tr', PHP_EOL . implode('', $cells) . PHP_EOL, $footerRowAttributes)
            ->encode(false)
            ->render();

        if ($this->filterPosition === self::FILTER_POS_FOOTER) {
            $content .= PHP_EOL . $this->renderFilters($columns);
        }

        return Html::tag('tfoot', PHP_EOL . $content . PHP_EOL)->encode(false)->render();
    }

    /**
     * Renders the table header.
     *
     * @param array $columns The columns of gridview.
     *
     * @psalm-param array<array-key,AbstractColumn>|array $columns
     */
    private function renderTableHeader(array $columns): string
    {
        $cell = [];
        $cells = '';

        foreach ($columns as $column) {
            if ($column instanceof AbstractColumn) {
                $cell[] = $column->renderHeaderCell();
            }
        }

        if ($cell !== []) {
            $cells = PHP_EOL . implode(PHP_EOL, $cell) . PHP_EOL;
        }

        $content = Html::tag('tr', $cells, $this->headerRowAttributes)->encode(false)->render();
        $renderFilters = PHP_EOL . $this->renderFilters($columns);

        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            $content = $renderFilters . PHP_EOL . $content;
        } elseif (self::FILTER_POS_BODY === $this->filterPosition) {
            $content .= $renderFilters;
        }

        return Html::tag('thead', PHP_EOL . trim($content) . PHP_EOL)->encode(false)->render();
    }

    /**
     * Renders a table row with the given data and key.
     *
     * @param array $columns The columns of gridview.
     * @param array|object $data The data.
     * @param mixed $key The key associated with the data.
     * @param int $index The zero-based index of the data in the data provider.
     *
     * @psalm-param array<array-key,AbstractColumn>|array $columns
     */
    private function renderTableRow(array $columns, array|object $data, mixed $key, int $index): string
    {
        $cells = [];
        $content = '';

        foreach ($columns as $column) {
            if ($column instanceof AbstractColumn) {
                $cells[] = $column->renderDataCell($data, $key, $index);
            }
        }

        if ($cells !== []) {
            $content = PHP_EOL . implode(PHP_EOL, $cells) . PHP_EOL;
        }

        return Html::tag('tr', $content, $this->rowAttributes)->encode(false)->render();
    }
}
