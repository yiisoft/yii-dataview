<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Closure;
use Psr\Container\ContainerInterface;
use Stringable;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Data\Reader\SortableDataInterface;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Tr;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result as ValidationResult;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\FilterContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;
use Yiisoft\Yii\DataView\Column\Base\MakeFilterContext;
use Yiisoft\Yii\DataView\Column\Base\RendererContainer;
use Yiisoft\Yii\DataView\Column\ColumnInterface;
use Yiisoft\Yii\DataView\Column\ColumnRendererInterface;
use Yiisoft\Yii\DataView\Column\FilterableColumnRendererInterface;
use Yiisoft\Yii\DataView\Column\OverrideOrderFieldsColumnInterface;
use Yiisoft\Yii\DataView\Filter\Factory\IncorrectValueException;

use function call_user_func;
use function call_user_func_array;
use function is_callable;

/**
 * The GridView widget displays data as a grid.
 *
 * You can configure the columns displayed by passing an array of {@see Column} instances to {@see GridView::columns()}.
 *
 * @psalm-import-type UrlCreator from BaseListView
 * @psalm-type BodyRowAttributes = array|(Closure(array|object, BodyRowContext): array)|(array<array-key, Closure(array|object, BodyRowContext): mixed>)
 */
final class GridView extends BaseListView
{
    private Closure|null $afterRowCallback = null;
    private Closure|null $beforeRowCallback = null;

    /**
     * @var ColumnInterface[]
     */
    private array $columns = [];

    /**
     * @var ColumnInterface[]
     */
    private ?array $columnsCache = null;

    private bool $isColumnGroupEnabled = false;
    private string $emptyCell = '&nbsp;';
    private array $emptyCellAttributes = [];
    private bool $footerEnabled = false;
    private array $footerRowAttributes = [];
    private bool $headerTableEnabled = true;
    private array $headerRowAttributes = [];
    /**
     * @psalm-var BodyRowAttributes
     */
    private Closure|array $bodyRowAttributes = [];
    private array $tableAttributes = [];
    private array $tbodyAttributes = [];
    private array $headerCellAttributes = [];
    private array $bodyCellAttributes = [];

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

    private array $filterCellAttributes = [];
    private ?string $filterCellInvalidClass = null;
    private array $filterErrorsContainerAttributes = [];

    private RendererContainer $columnRendererContainer;

    /**
     * @var ColumnRendererInterface[]
     */
    private ?array $columnRenderersCache = null;

    /**
     * @param ContainerInterface $columnRenderersDependencyContainer Container used to resolve
     * {@see ColumnRendererInterface column renderer} dependencies.
     * @param TranslatorInterface|null $translator Translator instance or `null` if no translation is needed.
     */
    public function __construct(
        ContainerInterface $columnRenderersDependencyContainer,
        TranslatorInterface|null $translator = null,
    ) {
        $this->columnRendererContainer = new RendererContainer($columnRenderersDependencyContainer);
        parent::__construct($translator);
    }

    /**
     * Add configurations for a column renderers.
     *
     * @psalm-param array<class-string, array> $configs An array of column renderer configurations. Keys are column
     * renderer class names. Values are arrays of constructor arguments either indexed by argument name or having
     * integer index if applied sequentially.
     */
    public function addColumnRendererConfigs(array $configs): self
    {
        $new = clone $this;
        $new->columnRendererContainer = $this->columnRendererContainer->addConfigs($configs);
        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the filter cell (`td`) tag.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    public function filterCellAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->filterCellAttributes = $attributes;
        return $new;
    }

    public function filterCellInvalidClass(?string $class): self
    {
        $new = clone $this;
        $new->filterCellInvalidClass = $class;
        return $new;
    }

    /**
     * TODO: docs
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    public function filterErrorsContainerAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->filterErrorsContainerAttributes = $attributes;
        return $new;
    }

    /**
     * Whether to keep the current page when sorting is changed.
     *
     * @param bool $enabled Whether to keep the current page.
     */
    public function keepPageOnSort(bool $enabled = true): self
    {
        $new = clone $this;
        $new->keepPageOnSort = $enabled;
        return $new;
    }

    /**
     * Returns a new instance with a callback that is executed once AFTER rendering each data row.
     *
     * @param Closure|null $callback The callback with the following signature:
     *
     * ```
     *
     * ```
     */
    public function afterRow(Closure|null $callback): self
    {
        $new = clone $this;
        $new->afterRowCallback = $callback;

        return $new;
    }

    /**
     * Return a new instance with a callback that is executed BEFORE rendering each data row.
     *
     * @param Closure|null $callback The callback with the following signature:
     *
     * ```
     * ```
     */
    public function beforeRow(Closure|null $callback): self
    {
        $new = clone $this;
        $new->beforeRowCallback = $callback;

        return $new;
    }

    /**
     * Return a new instance the specified column configurations.
     *
     * @param ColumnInterface ...$columns The grid column configuration. Each element represents the configuration
     * for one particular grid column. For example,
     *
     * ```php
     * [
     *     new SerialColumn(),
     *     new DetailColumn(),
     *     (new ActionColumn())->primaryKey('identity_id')->visibleButtons(['view' => true]),
     * ]
     * ```
     */
    public function columns(ColumnInterface ...$columns): self
    {
        $new = clone $this;
        $new->columns = $columns;
        return $new;
    }

    /**
     * Returns a new instance with the column grouping enabled.
     *
     * @param bool $enabled Whether to enable the column grouping.
     * @see https://developer.mozilla.org/docs/Web/HTML/Element/colgroup
     *
     * @see ColumnRendererInterface::renderColumn()
     */
    public function enableColumnGroup(bool $enabled = true): self
    {
        $new = clone $this;
        $new->isColumnGroupEnabled = $enabled;
        return $new;
    }

    /**
     * Return new instance with the HTML to display when the content is empty.
     *
     * @param string $content HTML content. Defaults to `&nbsp;`.
     * @param array|null $attributes The HTML attributes for the empty cell. Attribute values indexed by attribute names.
     */
    public function emptyCell(string $content, ?array $attributes = null): self
    {
        $new = clone $this;
        $new->emptyCell = $content;
        if ($attributes !== null) {
            $new->emptyCellAttributes = $attributes;
        }
        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the empty cell.
     *
     * @param array $attributes The HTML attributes for the empty cell. Attribute values indexed by attribute names.
     */
    public function emptyCellAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->emptyCellAttributes = $attributes;
        return $new;
    }

    /**
     * Whether to show the footer section of the grid.
     *
     * @param bool $enabled Whether to show the footer section of the grid.
     */
    public function footerEnabled(bool $enabled): self
    {
        $new = clone $this;
        $new->footerEnabled = $enabled;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for footer row.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    public function footerRowAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->footerRowAttributes = $attributes;

        return $new;
    }

    /**
     * Return new instance whether to show the header table section of the grid.
     *
     * @param bool $enabled Whether to show the header table section of the grid.
     */
    public function headerTableEnabled(bool $enabled): self
    {
        $new = clone $this;
        $new->headerTableEnabled = $enabled;

        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the header row.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    public function headerRowAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->headerRowAttributes = $attributes;

        return $new;
    }

    /**
     * Return new instance with the HTML attributes for body rows of the grid.
     *
     * @param array|Closure $attributes Attribute values indexed by attribute names or a callable that returns them. The
     * signature of the callable should be:
     *
     * ```php
     * function (array|object $data, BodyRowContext $context): array
     * ```
     *
     * If an array passed, the attribute values also can be a callable that returns them. The signature of the callable
     * should be:
     *
     * ```php
     * function (array|object $data, BodyRowContext $context): mixed
     * ```
     *
     * @psalm-param BodyRowAttributes $attributes
     */
    public function bodyRowAttributes(Closure|array $attributes): self
    {
        $new = clone $this;
        $new->bodyRowAttributes = $attributes;
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
    public function tableClass(?string ...$class): self
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
    public function tbodyClass(?string ...$class): self
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
     * Return new instance with the HTML attributes for a link in sortable columns' headers.
     *
     * @param array $attributes The tag attributes in terms of name-value pairs.
     */
    public function sortableLinkAttributes(array $attributes): self
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
    protected function renderItems(array $items, ValidationResult $filterValidationResult): string
    {
        $columns = $this->getColumns();
        $renderers = $this->getColumnRenderers();

        $blocks = [];
        $filtersForm = '';

        $dataReader = $this->getDataReader();
        $globalContext = new GlobalContext(
            $dataReader,
            $this->urlArguments,
            $this->urlQueryParameters,
            $this->translator,
            $this->translationCategory,
        );

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

        $tags = [];
        $hasFilters = false;
        $filterContext = new FilterContext(
            formId: Html::generateId(),
            validationResult: $filterValidationResult,
            cellInvalidClass: $this->filterCellInvalidClass,
            errorsContainerAttributes: $this->filterErrorsContainerAttributes,
            urlParameterProvider: $this->urlParameterProvider,
        );
        foreach ($columns as $i => $column) {
            $cell = $renderers[$i] instanceof FilterableColumnRendererInterface
                ? $renderers[$i]->renderFilter($column, new Cell(attributes: $this->filterCellAttributes), $filterContext)
                : null;
            if ($cell === null) {
                $tags[] = Html::td('&nbsp;')->encode(false);
            } else {
                $tags[] = Html::td(attributes: $cell->getAttributes())
                    ->content(...$cell->getContent())
                    ->encode($cell->isEncode())
                    ->doubleEncode($cell->isDoubleEncode());
                $hasFilters = true;
            }
        }
        if ($hasFilters) {
            $sort = $this->urlParameterProvider?->get(
                $this->urlConfig->getSortParameterName(),
                $this->urlConfig->getSortParameterType()
            );
            $url = $this->urlCreator === null ? '' : call_user_func_array(
                $this->urlCreator,
                UrlParametersFactory::create(
                    null,
                    $this->urlConfig->getPageSizeParameterType() === UrlParameterType::PATH ? $pageSize : null,
                    $this->urlConfig->getSortParameterType() === UrlParameterType::PATH ? $sort : null,
                    $this->urlConfig,
                )
            );
            $content = [Html::submitButton()];
            if (!empty($pageSize) && $this->urlConfig->getPageSizeParameterType() === UrlParameterType::QUERY) {
                $content[] = Html::hiddenInput($this->urlConfig->getPageSizeParameterName(), $pageSize);
            }
            if (!empty($sort) && $this->urlConfig->getSortParameterType() === UrlParameterType::QUERY) {
                $content[] = Html::hiddenInput($this->urlConfig->getSortParameterName(), $sort);
            }
            $filtersForm = Html::form($url, 'GET', ['id' => $filterContext->formId, 'style' => 'display:none'])
                ->content(...$content)
                ->render();
            $filterRow = Html::tr()->cells(...$tags);
        } else {
            $filterRow = null;
        }

        if ($this->isColumnGroupEnabled) {
            $tags = [];
            foreach ($columns as $i => $column) {
                $cell = $renderers[$i]->renderColumn($column, new Cell(), $globalContext);
                $tags[] = Html::col($cell->getAttributes());
            }
            $blocks[] = Html::colgroup()->columns(...$tags)->render();
        }

        if ($this->headerTableEnabled) {
            $headerContext = new HeaderContext(
                $this->getSort($dataReader),
                $this->getSort($this->preparedDataReader),
                $this->getOverrideOrderFields(),
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
                        ->content(...$cell->getContent())
                        ->encode($cell->isEncode())
                        ->doubleEncode($cell->isDoubleEncode());
            }
            $headerRow = Html::tr($this->headerRowAttributes)->cells(...$tags);
            $thead = Html::thead()->rows($headerRow);
            if ($filterRow !== null) {
                $thead = $thead->addRows($filterRow);
            }
            $blocks[] = $thead->render();
        }

        if ($this->footerEnabled) {
            $tags = [];
            foreach ($columns as $i => $column) {
                $cell = $renderers[$i]->renderFooter(
                    $column,
                    (new Cell())->content('&nbsp;')->encode(false),
                    $globalContext
                );
                $tags[] = Html::td(attributes: $cell->getAttributes())
                    ->content(...$cell->getContent())
                    ->encode($cell->isEncode())
                    ->doubleEncode($cell->isDoubleEncode());
            }
            $footerRow = Html::tr($this->footerRowAttributes)->cells(...$tags);
            $blocks[] = Html::tfoot()->rows($footerRow)->render();
        }

        $rows = [];
        $index = 0;
        foreach ($items as $key => $value) {
            if ($this->beforeRowCallback !== null) {
                /** @var Tr|null $row */
                $row = call_user_func($this->beforeRowCallback, $value, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $tags = [];
            foreach ($columns as $i => $column) {
                $context = new DataContext($column, $value, $key, $index);
                $cell = $renderers[$i]->renderBody($column, new Cell($this->bodyCellAttributes), $context);
                $tags[] = $cell->isEmptyContent()
                    ? Html::td($this->emptyCell, $this->emptyCellAttributes)->encode(false)
                    : Html::td(attributes: $this->prepareBodyAttributes($cell->getAttributes(), $context))
                        ->content(...$cell->getContent())
                        ->encode($cell->isEncode())
                        ->doubleEncode($cell->isDoubleEncode());
            }
            $bodyRowAttributes = $this->prepareBodyRowAttributes(
                $this->bodyRowAttributes,
                new BodyRowContext($value, $key, $index),
            );
            $rows[] = Html::tr($bodyRowAttributes)->cells(...$tags);

            if ($this->afterRowCallback !== null) {
                /** @var Tr|null $row */
                $row = call_user_func($this->afterRowCallback, $value, $key, $index, $this);
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

        return
            $filtersForm .
            Html::tag('table', attributes: $this->tableAttributes)->open()
            . "\n"
            . implode("\n", $blocks)
            . "\n"
            . '</table>';
    }

    protected function makeFilters(): array
    {
        $columns = $this->getColumns();
        $renderers = $this->getColumnRenderers();

        $validationResult = new ValidationResult();
        $context = new MakeFilterContext($validationResult, $this->urlParameterProvider);

        $filters = [];
        foreach ($columns as $i => $column) {
            if ($renderers[$i] instanceof FilterableColumnRendererInterface) {
                try {
                    $filter = $renderers[$i]->makeFilter($column, $context);
                } catch (IncorrectValueException) {
                    $filters = null;
                    break;
                }
                if ($filter !== null) {
                    $filters[] = $filter;
                }
            }
        }

        return [$filters, $validationResult];
    }

    protected function prepareOrder(array &$order): void
    {
        $columns = $this->getColumns();
        $renderers = $this->getColumnRenderers();
        foreach ($columns as $i => $column) {
            if ($renderers[$i] instanceof OverrideOrderFieldsColumnInterface) {
                foreach ($renderers[$i]->getOverrideOrderFields($column) as $from => $to) {
                    $order = ArrayHelper::renameKey($order, $from, $to);
                }
            }
        }
    }

    protected function getOverrideOrderFields(): array
    {
        $columns = $this->getColumns();
        $renderers = $this->getColumnRenderers();

        $overrideOrderFields = [];
        foreach ($columns as $i => $column) {
            if ($renderers[$i] instanceof OverrideOrderFieldsColumnInterface) {
                $overrideOrderFields[] = $renderers[$i]->getOverrideOrderFields($column);
            }
        }

        return array_merge(...$overrideOrderFields);
    }

    /**
     * @psalm-param BodyRowAttributes $attributes
     */
    private function prepareBodyRowAttributes(array|Closure $attributes, BodyRowContext $context): array
    {
        if (is_callable($attributes)) {
            return $attributes($context->data, $context);
        }

        return array_map(
            static fn ($attribute): mixed => is_callable($attribute)
                ? $attribute($context->data, $context)
                : $attribute,
            $attributes,
        );
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

    /**
     * @return ColumnInterface[]
     */
    private function getColumns(): array
    {
        if ($this->columnsCache === null) {
            $this->columnsCache = array_filter(
                $this->columns,
                static fn(ColumnInterface $column) => $column->isVisible()
            );
        }

        return $this->columnsCache;
    }

    /**
     * @return ColumnRendererInterface[]
     */
    private function getColumnRenderers(): array
    {
        if ($this->columnRenderersCache === null) {
            $this->columnRenderersCache = array_map(
                fn(ColumnInterface $column) => $this->columnRendererContainer->get($column->getRenderer()),
                $this->getColumns()
            );
        }

        return $this->columnRenderersCache;
    }
}
