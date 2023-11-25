<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Closure;
use Psr\Container\ContainerInterface;
use Stringable;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Tr;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\ColumnInterface;
use Yiisoft\Yii\DataView\Column\ColumnRendererInterface;
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

    /**
     * @var ColumnInterface[]
     */
    private array $columns = [];

    private bool $columnsGroupEnabled = false;
    private string $emptyCell = '&nbsp;';
    private ?string $filterModelName = null;
    private string $filterPosition = self::FILTER_POS_BODY;
    private array $filterRowAttributes = [];
    private bool $footerEnabled = false;
    private array $footerRowAttributes = [];
    private bool $headerTableEnabled = true;
    private array $headerRowAttributes = [];
    private array $rowAttributes = [];
    private array $tableAttributes = [];
    private array $tbodyAttributes = [];
    private array $headerCellAttributes = [];
    private array $bodyCellAttributes = [];

    public function __construct(
        private ContainerInterface $columnRenderersContainer,
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
     * @param ColumnInterface ...$values The grid column configuration. Each array element represents the configuration
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
    public function columns(ColumnInterface ...$values): self
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
     * @param string|null $value The form model name that keeps the user-entered filter data. When this property is set, the
     * grid view will enable column-based filtering. Each data column by default will display a text field at the top
     * that users can fill in to filter the data.
     *
     * Note that in order to show an input field for filtering, a column must have its {@see DataColumn::attribute}
     * property set and the attribute should be active in the current scenario of $filterModelName or have
     * {@see DataColumn::filter} set as the HTML code for the input field.
     */
    public function filterModelName(?string $value): self
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
     * Renders the data active record classes for the grid view.
     *
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    protected function renderItems(): string
    {
        $columns = empty($this->columns) ? $this->guessColumns() : $this->columns;
        $columns = array_filter(
            $columns,
            static fn(ColumnInterface $column) => $column->isVisible()
        );

        $renderers = [];
        foreach ($columns as $i => $column) {
            $renderers[$i] = $this->getColumnRenderer($column);
        }

        $blocks = [];

        $globalContext = new GlobalContext(
            $this->getDataReader(),
            $this->sortLinkAttributes,
            $this->urlArguments,
            $this->urlQueryParameters,
            $this->filterModelName,
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

        if ($this->filterPosition === self::FILTER_POS_BODY
            || $this->filterPosition === self::FILTER_POS_HEADER
            || $this->filterPosition === self::FILTER_POS_FOOTER
        ) {
            $tags = [];
            $hasFilters = false;
            foreach ($columns as $i => $column) {
                $baseCell = new Cell(encode: false, content: '&nbsp;');
                $cell = $renderers[$i]->renderFilter($column, $baseCell, $globalContext);
                if ($cell === null) {
                    $cell = $baseCell;
                } else {
                    $hasFilters = true;
                }
                /** @var string|Stringable $content */
                $content = $cell->getContent();
                $tags[] = Html::td(attributes: $cell->getAttributes())
                    ->content($content)
                    ->encode($cell->isEncode())
                    ->doubleEncode($cell->isDoubleEncode());
            }
            $filterRow = $hasFilters ? Html::tr($this->filterRowAttributes)->cells(...$tags) : null;
        } else {
            $filterRow = null;
        }

        if ($this->headerTableEnabled) {
            $tags = [];
            foreach ($columns as $i => $column) {
                $cell = $renderers[$i]->renderHeader($column, new Cell($this->headerCellAttributes), $globalContext);
                /** @var string|Stringable $content */
                $content = $cell?->getContent();
                $tags[] = $cell === null
                    ? Html::th('&nbsp;')->encode(false)
                    : Html::th(attributes: $cell->getAttributes())
                        ->content($content)
                        ->encode($cell->isEncode())
                        ->doubleEncode($cell->isDoubleEncode());
            }
            $headerRow = Html::tr($this->headerRowAttributes)->cells(...$tags);

            if ($filterRow === null) {
                $rows = [$headerRow];
            } elseif ($this->filterPosition === self::FILTER_POS_HEADER) {
                $rows = [$filterRow, $headerRow];
            } elseif ($this->filterPosition === self::FILTER_POS_BODY) {
                $rows = [$headerRow, $filterRow];
            } else {
                $rows = [$headerRow];
            }

            $blocks[] = Html::thead()->rows(...$rows)->render();
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

            $rows = [$footerRow];
            if ($this->filterPosition === self::FILTER_POS_FOOTER) {
                /** @var Tr */
                $rows[] = $filterRow;
            }

            $blocks[] = Html::tfoot()->rows(...$rows)->render();
        }

        $rows = [];
        $index = 0;
        foreach ($this->getItems() as $key => $value) {
            if ($this->beforeRow !== null) {
                /** @var Tr|null $row */
                $row = call_user_func($this->beforeRow, $value, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $tags = [];
            foreach ($columns as $i => $column) {
                $context = new DataContext($column, $value, $key, $index);
                $cell = $renderers[$i]->renderBody($column, new Cell(), $context);
                $contentSource = $cell->getContent();
                /** @var string|Stringable $content */
                $content = $contentSource instanceof Closure
                    ? $contentSource($context)
                    : $contentSource;
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
            . PHP_EOL
            . implode(PHP_EOL, $blocks)
            . PHP_EOL
            . '</table>';
    }

    /**
     * This function tries to guess the columns to show from the given data if {@see columns} are not explicitly
     * specified.
     *
     * @psalm-return list<ColumnInterface>
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
}
