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
    /**
     * @var Closure|null Callback executed after rendering each data row.
     */
    private Closure|null $afterRowCallback = null;

    /**
     * @var Closure|null Callback executed before rendering each data row.
     */
    private Closure|null $beforeRowCallback = null;

    /**
     * @var ColumnInterface[] Grid column configurations.
     */
    private array $columns = [];

    /**
     * @var ColumnInterface[]|null Cache of resolved columns.
     */
    private ?array $columnsCache = null;

    /**
     * @var bool Whether column grouping is enabled.
     */
    private bool $isColumnGroupEnabled = false;

    /**
     * @var string HTML content for empty cells.
     */
    private string $emptyCell = '&nbsp;';

    /**
     * @var array HTML attributes for empty cells
     */
    private array $emptyCellAttributes = [];

    /**
     * @var bool Whether footer section is enabled.
     */
    private bool $isFooterEnabled = false;

    /**
     * @var array HTML attributes for footer row
     */
    private array $footerRowAttributes = [];

    /**
     * @var bool Whether header table section is enabled.
     */
    private bool $isHeaderTableEnabled = true;

    /**
     * @var array HTML attributes for header row
     */
    private array $headerRowAttributes = [];

    /**
     * @var array|Closure HTML attributes for body rows.
     * @psalm-var BodyRowAttributes
     */
    private Closure|array $bodyRowAttributes = [];

    /**
     * @var array HTML attributes for the table tag.
     */
    private array $tableAttributes = [];

    /**
     * @var array HTML attributes for the tbody tag.
     */
    private array $tbodyAttributes = [];

    /**
     * @var array HTML attributes for the header cell (th) tag.
     */
    private array $headerCellAttributes = [];

    /**
     * @var array HTML attributes for the body cell (td) tag.
     */
    private array $bodyCellAttributes = [];

    /**
     * @var bool Whether to keep the current page when sorting is changed.
     */
    private bool $keepPageOnSort = false;

    /**
     * @var string|null CSS class for sortable headers.
     */
    private ?string $sortableHeaderClass = null;

    /**
     * @var string|Stringable Content to be prepended to sortable headers.
     */
    private string|Stringable $sortableHeaderPrepend = '';

    /**
     * @var string|Stringable Content to be appended to sortable headers.
     */
    private string|Stringable $sortableHeaderAppend = '';

    /**
     * @var string|null CSS class for ascending sorted headers.
     */
    private ?string $sortableHeaderAscClass = null;

    /**
     * @var string|Stringable Content to be prepended to ascending sorted headers.
     */
    private string|Stringable $sortableHeaderAscPrepend = '';

    /**
     * @var string|Stringable Content to be appended to ascending sorted headers.
     */
    private string|Stringable $sortableHeaderAscAppend = '';

    /**
     * @var string|null CSS class for descending sorted headers.
     */
    private ?string $sortableHeaderDescClass = null;

    /**
     * @var string|Stringable Content to be prepended to descending sorted headers.
     */
    private string|Stringable $sortableHeaderDescPrepend = '';

    /**
     * @var string|Stringable Content to be appended to descending sorted headers.
     */
    private string|Stringable $sortableHeaderDescAppend = '';

    /**
     * @var array HTML attributes for sortable links.
     */
    private array $sortableLinkAttributes = [];

    /**
     * @var string|null CSS class for ascending sort links.
     */
    private ?string $sortableLinkAscClass = null;

    /**
     * @var string|null CSS class for descending sort links.
     */
    private ?string $sortableLinkDescClass = null;

    /**
     * @var array HTML attributes for filter cells.
     */
    private array $filterCellAttributes = [];

    /**
     * @var string|null CSS class for invalid filter cells.
     */
    private ?string $filterCellInvalidClass = null;

    /**
     * @var array HTML attributes for filter error containers.
     */
    private array $filterErrorsContainerAttributes = [];

    /**
     * @var RendererContainer Container for column renderers.
     */
    private RendererContainer $columnRendererContainer;

    /**
     * @var ColumnRendererInterface[]|null Cache of column renderers.
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
     *
     * @return self New instance with the added column renderer configurations.
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
     *
     * @return self New instance with the filter cell attributes.
     */
    public function filterCellAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->filterCellAttributes = $attributes;
        return $new;
    }

    /**
     * Return new instance with the CSS class to be added to filter cells that contain invalid values.
     *
     * @param string|null $class The CSS class name. Set to null to remove the class.
     *
     * @return self New instance with the filter cell invalid class.
     */
    public function filterCellInvalidClass(?string $class): self
    {
        $new = clone $this;
        $new->filterCellInvalidClass = $class;
        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the filter errors container.
     * This container will be rendered below the filter cell when validation fails.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     *
     * @return self New instance with the filter errors container attributes.
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
     *
     * @return self New instance with the keep page on sort setting.
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
     * ```php
     * function (array|object $data, array|int|string $key, int $index, GridView $grid): ?Tr
     * ```
     *
     * - `$data`: the data model being rendered
     * - `$key`: the key associated with the data model
     * - `$index`: the zero-based index of the data model in the model array
     * - `$grid`: the GridView object
     *
     * The callback should return either:
     * - a {@see Tr} instance representing a table row to be rendered after the data row
     * - or null if no additional row should be rendered
     *
     * @return self New instance with the after row callback.
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
     * ```php
     * function (array|object $data, array|int|string $key, int $index, GridView $grid): ?Tr
     * ```
     *
     * - `$data`: the data model being rendered
     * - `$key`: the key associated with the data model
     * - `$index`: the zero-based index of the data model in the model array
     * - `$grid`: the GridView object
     *
     * The callback should return either:
     * - a {@see Tr} instance representing a table row to be rendered before the data row
     * - or null if no additional row should be rendered
     *
     * @return self New instance with the before row callback.
     */
    public function beforeRow(Closure|null $callback): self
    {
        $new = clone $this;
        $new->beforeRowCallback = $callback;

        return $new;
    }

    /**
     * Return new instance with the specified column configurations.
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
     *
     * @return self New instance with the column configurations.
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
     *
     * @return self New instance with the column grouping enabled.
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
     *
     * @return self New instance with the empty cell content.
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
     *
     * @return self New instance with the empty cell attributes.
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
     *
     * @return self New instance with the footer section visibility setting.
     */
    public function footerEnabled(bool $enabled): self
    {
        $new = clone $this;
        $new->isFooterEnabled = $enabled;
        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for footer row.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     *
     * @return self New instance with the footer row attributes.
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
     *
     * @return self New instance with the header table section visibility setting.
     */
    public function enableHeaderTable(bool $enabled = true): self
    {
        $new = clone $this;
        $new->isHeaderTableEnabled = $enabled;
        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the header row.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     *
     * @return self New instance with the header row attributes.
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
     *
     * @return self New instance with the body row attributes.
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
     *
     * @return self New instance with the table attributes.
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
     *
     * @return self New instance with the added table CSS classes.
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
     *
     * @return self New instance with the replaced table CSS classes.
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
     *
     * @return self New instance with the tbody attributes.
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
     *
     * @return self New instance with the added tbody CSS classes.
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
     *
     * @return self New instance with the replaced tbody CSS classes.
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
     *
     * @return self New instance with the header cell attributes.
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
     *
     * @return self New instance with the body cell attributes.
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
     *
     * @return self New instance with the sortable link attributes.
     */
    public function sortableLinkAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->sortableLinkAttributes = $attributes;
        return $new;
    }

    /**
     * Return new instance with the content to be prepended to sortable column headers.
     *
     * @param string|Stringable $content The content to prepend.
     *
     * @return self New instance with the sortable header prepend content.
     */
    public function sortableHeaderPrepend(string|Stringable $content): self
    {
        $new = clone $this;
        $new->sortableHeaderPrepend = $content;
        return $new;
    }

    /**
     * Return new instance with the content to be appended to sortable column headers.
     *
     * @param string|Stringable $content The content to append.
     *
     * @return self New instance with the sortable header append content.
     */
    public function sortableHeaderAppend(string|Stringable $content): self
    {
        $new = clone $this;
        $new->sortableHeaderAppend = $content;
        return $new;
    }

    /**
     * Return new instance with the content to be prepended to ascending sorted column headers.
     *
     * @param string|Stringable $content The content to prepend.
     *
     * @return self New instance with the sortable header ascending prepend content.
     */
    public function sortableHeaderAscPrepend(string|Stringable $content): self
    {
        $new = clone $this;
        $new->sortableHeaderAscPrepend = $content;
        return $new;
    }

    /**
     * Return new instance with the content to be appended to ascending sorted column headers.
     *
     * @param string|Stringable $content The content to append.
     *
     * @return self New instance with the sortable header ascending append content.
     */
    public function sortableHeaderAscAppend(string|Stringable $content): self
    {
        $new = clone $this;
        $new->sortableHeaderAscAppend = $content;
        return $new;
    }

    /**
     * Return new instance with the content to be prepended to descending sorted column headers.
     *
     * @param string|Stringable $content The content to prepend.
     *
     * @return self New instance with the sortable header descending prepend content.
     */
    public function sortableHeaderDescPrepend(string|Stringable $content): self
    {
        $new = clone $this;
        $new->sortableHeaderDescPrepend = $content;
        return $new;
    }

    /**
     * Return new instance with the content to be appended to descending sorted column headers.
     *
     * @param string|Stringable $content The content to append.
     *
     * @return self New instance with the sortable header descending append content.
     */
    public function sortableHeaderDescAppend(string|Stringable $content): self
    {
        $new = clone $this;
        $new->sortableHeaderDescAppend = $content;
        return $new;
    }

    /**
     * Renders the data items for the grid view.
     *
     * @param array $items The data items to be rendered.
     * @param ValidationResult $filterValidationResult The validation result for filters.
     *
     * @return string The rendered HTML content.
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

        if ($this->isHeaderTableEnabled) {
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

        if ($this->isFooterEnabled) {
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

    /**
     * Makes filters for the grid view.
     *
     * @return array{0: array, 1: ValidationResult} The prepared filters and validation result.
     */
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

    /**
     * Prepares the order configuration.
     *
     * @param array $order The order configuration to prepare.
     */
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

    /**
     * Gets the override order fields.
     *
     * @return array The override order fields.
     */
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
     * Prepares the body row attributes.
     *
     * @param array|Closure $attributes The attributes to prepare.
     * @param BodyRowContext $context The body row context.
     *
     * @return array The prepared attributes.
     *
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

    /**
     * Prepares the body attributes.
     *
     * @param array $attributes The attributes to prepare.
     * @param DataContext $context The data context.
     *
     * @return array The prepared attributes.
     */
    private function prepareBodyAttributes(array $attributes, DataContext $context): array
    {
        foreach ($attributes as $i => $attribute) {
            if (is_callable($attribute)) {
                $attributes[$i] = $attribute($context);
            }
        }

        return $attributes;
    }

    /**
     * Gets the sort configuration.
     *
     * @param ReadableDataInterface|null $dataReader The data reader.
     *
     * @return Sort|null The sort configuration.
     */
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
     * Gets the columns.
     *
     * @return ColumnInterface[] The columns.
     */
    protected function getColumns(): array
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
     * Gets the column renderers.
     *
     * @return ColumnRendererInterface[] The column renderers.
     */
    protected function getColumnRenderers(): array
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
