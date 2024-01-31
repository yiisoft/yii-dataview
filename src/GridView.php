<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Closure;
use Psr\Container\ContainerInterface;
use Stringable;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Data\Reader\SortableDataInterface;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Tr;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;
use Yiisoft\Yii\DataView\Column\ColumnInterface;
use Yiisoft\Yii\DataView\Column\ColumnRendererInterface;
use Yiisoft\Yii\DataView\Column\DataColumn;

/**
 * The GridView widget is used to display data in a grid.
 *
 * The columns of the grid table are configured in terms of {@see Column} classes, which are configured via
 * {@see columns}.
 *
 * The look and feel of a grid view can be customized using the large amount of properties.
 *
 * @psalm-import-type UrlCreator from BaseListView
 */
final class GridView extends BaseListView
{
    private Closure|null $afterRow = null;
    private Closure|null $beforeRow = null;

    /**
     * @var ColumnInterface[]
     */
    private array $columns = [];
    private array $columnsConfigs = [];

    private bool $columnsGroupEnabled = false;
    private string $emptyCell = '&nbsp;';
    private bool $footerEnabled = false;
    private array $footerRowAttributes = [];
    private bool $headerTableEnabled = true;
    private array $headerRowAttributes = [];
    private array $rowAttributes = [];
    private array $tableAttributes = [];
    private array $tbodyAttributes = [];
    private array $headerCellAttributes = [];
    private array $bodyCellAttributes = [];

    private bool $enableMultiSort = false;
    private bool $keepPageOnSort = false;
    private ?string $sortableHeaderClass = null;
    private string|Stringable $sortableHeaderPrepend = '';
    private string|Stringable $sortableHeaderAppend = '';
    private ?string $sortableHeaderAscClass = null;
    private string|Stringable $sortableHeaderAscPrepend = '';
    private string|Stringable $sortableHeaderAscAppend = '';
    private ?string $sortableHeaderDescClass = null;
    private string|Stringable $sortableHeaderDescPrepend = '';
    private string|Stringable $sortableHeaderDescAppend = '';
    private array $sortableLinkAttributes = [];
    private ?string $sortableLinkAscClass = null;
    private ?string $sortableLinkDescClass = null;

    public function __construct(
        private ContainerInterface $columnRenderersContainer,
        TranslatorInterface|null $translator = null,
    ) {
        parent::__construct($translator);
    }

    public function enableMultiSort(bool $value = true): self
    {
        $new = clone $this;
        $new->enableMultiSort = $value;
        return $new;
    }

    public function keepPageOnSort(bool $value = true): self
    {
        $new = clone $this;
        $new->keepPageOnSort = $value;
        return $new;
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
     * @param ColumnInterface ...$values The grid column configuration. Each array element represents the configuration
     * for one particular grid column. For example,
     *
     * ```php
     * [
     *     SerialColumn::create(),
     *     DetailColumn::create(),
     *     ActionColumn::create()->primaryKey('identity_id')->visibleButtons(['view' => true]),
     * ]
     * ```
     */
    public function columns(ColumnInterface ...$values): self
    {
        $new = clone $this;
        $new->columns = $values;
        return $new;
    }

    public function columnsConfigs(array $configs): self
    {
        $new = clone $this;
        $new->columnsConfigs = $configs;
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
     * that have no defined content, e.g. empty footer.
     */
    public function emptyCell(string $value): self
    {
        $new = clone $this;
        $new->emptyCell = $value;

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
     * Return new instance with the HTML attributes for the `th` tag.
     *
     * @param array $attributes The tag attributes in terms of name-value pairs.
     */
    public function headerCellAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->headerCellAttributes = $attributes;
        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the `td` tag.
     *
     * @param array $attributes The tag attributes in terms of name-value pairs.
     */
    public function bodyCellAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->bodyCellAttributes = $attributes;
        return $new;
    }

    /**
     * Return new instance with the HTML attributes for link in sortable columns' headers.
     *
     * @param array $attributes The tag attributes in terms of name-value pairs.
     */
    public function sortableLinkAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->sortableLinkAttributes = $attributes;
        return $new;
    }

    public function sortableHeaderPrepend(string|Stringable $content): self
    {
        $new = clone $this;
        $new->sortableHeaderPrepend = $content;
        return $new;
    }

    public function sortableHeaderAppend(string|Stringable $content): self
    {
        $new = clone $this;
        $new->sortableHeaderAppend = $content;
        return $new;
    }

    public function sortableHeaderAscPrepend(string|Stringable $content): self
    {
        $new = clone $this;
        $new->sortableHeaderAscPrepend = $content;
        return $new;
    }

    public function sortableHeaderAscAppend(string|Stringable $content): self
    {
        $new = clone $this;
        $new->sortableHeaderAscAppend = $content;
        return $new;
    }

    public function sortableHeaderDescPrepend(string|Stringable $content): self
    {
        $new = clone $this;
        $new->sortableHeaderDescPrepend = $content;
        return $new;
    }

    public function sortableHeaderDescAppend(string|Stringable $content): self
    {
        $new = clone $this;
        $new->sortableHeaderDescAppend = $content;
        return $new;
    }

    /**
     * Renders the data active record classes for the grid view.
     */
    protected function renderItems(array $items): string
    {
        $columns = empty($this->columns) ? $this->guessColumns($items) : $this->columns;
        $columns = array_filter(
            $columns,
            static fn(ColumnInterface $column) => $column->isVisible()
        );

        $renderers = [];
        foreach ($columns as $i => $column) {
            $renderers[$i] = $this->getColumnRenderer($column);
        }

        $blocks = [];

        $dataReader = $this->getDataReader();
        $globalContext = new GlobalContext(
            $dataReader,
            $this->urlArguments,
            $this->urlQueryParameters,
            $this->columnsConfigs,
            $this->translator,
            $this->translationCategory,
        );

        if ($this->columnsGroupEnabled) {
            $tags = [];
            foreach ($columns as $i => $column) {
                $cell = $renderers[$i]->renderColumn($column, new Cell(), $globalContext);
                $tags[] = Html::col($cell->getAttributes());
            }
            $blocks[] = Html::colgroup()->columns(...$tags)->render();
        }

        if ($this->headerTableEnabled) {
            if ($this->preparedDataReader instanceof PaginatorInterface) {
                $pageToken = $this->preparedDataReader->isOnFirstPage() ? null : $this->preparedDataReader->getToken();
                $pageSize = $this->preparedDataReader->getPageSize();
                if ($pageSize === $this->getDefaultPageSize()) {
                    $pageSize = null;
                }
            } else {
                $pageToken = null;
                $pageSize = null;
            }
            $headerContext = new HeaderContext(
                $this->getSort($this->preparedDataReader),
                $this->sortableHeaderClass,
                $this->sortableHeaderPrepend,
                $this->sortableHeaderAppend,
                $this->sortableHeaderAscClass,
                $this->sortableHeaderAscPrepend,
                $this->sortableHeaderAscAppend,
                $this->sortableHeaderDescClass,
                $this->sortableHeaderDescPrepend,
                $this->sortableHeaderDescAppend,
                $this->sortableLinkAttributes,
                $this->sortableLinkAscClass,
                $this->sortableLinkDescClass,
                $this->keepPageOnSort ? $pageToken : null,
                $pageSize,
                $this->enableMultiSort,
                $this->urlConfig,
                $this->urlCreator,
                $this->translator,
                $this->translationCategory,
            );
            $tags = [];
            foreach ($columns as $i => $column) {
                $cell = $renderers[$i]->renderHeader($column, new Cell($this->headerCellAttributes), $headerContext);
                $tags[] = $cell === null
                    ? Html::th('&nbsp;')->encode(false)
                    : Html::th(attributes: $cell->getAttributes())
                        ->content($cell->getContent())
                        ->encode($cell->isEncode())
                        ->doubleEncode($cell->isDoubleEncode());
            }
            $headerRow = Html::tr($this->headerRowAttributes)->cells(...$tags);
            $blocks[] = Html::thead()->rows($headerRow)->render();
        }

        if ($this->footerEnabled) {
            $tags = [];
            foreach ($columns as $i => $column) {
                $cell = $renderers[$i]->renderFooter(
                    $column,
                    (new Cell())->content('&nbsp;')->encode(false),
                    $globalContext
                );
                /** @var string|Stringable $content */
                $content = $cell->getContent();
                $tags[] = Html::td(attributes: $cell->getAttributes())
                    ->content($content)
                    ->encode($cell->isEncode())
                    ->doubleEncode($cell->isDoubleEncode());
            }
            $footerRow = Html::tr($this->footerRowAttributes)->cells(...$tags);
            $blocks[] = Html::tfoot()->rows($footerRow)->render();
        }

        $rows = [];
        $index = 0;
        foreach ($items as $key => $value) {
            if ($this->beforeRow !== null) {
                /** @var Tr|null $row */
                $row = call_user_func($this->beforeRow, $value, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $tags = [];
            foreach ($columns as $i => $column) {
                $context = new DataContext($column, $value, $key, $index, $this->columnsConfigs);
                $cell = $renderers[$i]->renderBody($column, new Cell(), $context);
                $content = $cell->getContent();
                $tags[] = empty($content)
                    ? Html::td()->content($this->emptyCell)->encode(false)
                    : Html::td(attributes: $this->prepareBodyAttributes($cell->getAttributes(), $context))
                        ->content($content)
                        ->encode($cell->isEncode())
                        ->doubleEncode($cell->isDoubleEncode());
            }
            $rows[] = Html::tr($this->rowAttributes)->cells(...$tags);

            if ($this->afterRow !== null) {
                /** @var Tr|null $row */
                $row = call_user_func($this->afterRow, $value, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $index++;
        }
        $blocks[] = empty($rows)
            ? Html::tbody($this->tbodyAttributes)
                ->rows(Html::tr()->cells($this->renderEmpty(count($columns))))
                ->render()
            : Html::tbody($this->tbodyAttributes)->rows(...$rows)->render();

        return Html::tag('table', attributes: $this->tableAttributes)->open()
            . "\n"
            . implode("\n", $blocks)
            . "\n"
            . '</table>';
    }

    /**
     * This function tries to guess the columns to show from the given data if {@see columns} are not explicitly
     * specified.
     *
     * @psalm-return list<ColumnInterface>
     */
    private function guessColumns(array $items): array
    {
        $columns = [];
        foreach ($items as $item) {
            /**
             * @var string $name
             * @var mixed $value
             */
            foreach ($item as $name => $value) {
                if ($value === null || is_scalar($value) || is_callable([$value, '__toString'])) {
                    $columns[] = new DataColumn(property: $name);
                }
            }
            break;
        }

        if (!empty($items)) {
            $columns[] = new ActionColumn();
        }

        return $columns;
    }

    private function prepareBodyAttributes(array $attributes, DataContext $context): array
    {
        foreach ($attributes as $i => $attribute) {
            if (is_callable($attribute)) {
                $attributes[$i] = $attribute($context);
            }
        }

        return $attributes;
    }

    private function getColumnRenderer(ColumnInterface $column): ColumnRendererInterface
    {
        /** @var ColumnRendererInterface */
        return $this->columnRenderersContainer->get($column->getRenderer());
    }

    private function getSort(?ReadableDataInterface $dataReader): ?Sort
    {
        if ($dataReader instanceof PaginatorInterface && $dataReader->isSortable()) {
            return $dataReader->getSort();
        }

        if ($dataReader instanceof SortableDataInterface) {
            return $dataReader->getSort();
        }

        return null;
    }
}
