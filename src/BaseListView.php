<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use InvalidArgumentException;
use Stringable;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PageNotFoundException;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Data\Reader\CountableDataInterface;
use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\FilterableDataInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\LimitableDataInterface;
use Yiisoft\Data\Reader\OffsetableDataInterface;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Data\Reader\SortableDataInterface;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Div;
use Yiisoft\Html\Tag\Td;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IdMessageReader;
use Yiisoft\Translator\IntlMessageFormatter;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result as ValidationResult;
use Yiisoft\Widget\Widget;
use Yiisoft\Data\Reader\OrderHelper;
use Yiisoft\Yii\DataView\Exception\DataReaderNotSetException;
use Yiisoft\Yii\DataView\PageSize\InputPageSize;
use Yiisoft\Yii\DataView\PageSize\PageSizeContext;
use Yiisoft\Yii\DataView\PageSize\PageSizeWidgetInterface;
use Yiisoft\Yii\DataView\PageSize\SelectPageSize;
use Yiisoft\Yii\DataView\Pagination\KeysetPagination;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\DataView\Pagination\PaginationContext;
use Yiisoft\Yii\DataView\Pagination\PaginationWidgetInterface;
use Yiisoft\Yii\DataView\Pagination\PaginatorNotSupportedException;

use function array_key_exists;
use function array_slice;
use function call_user_func_array;
use function extension_loaded;
use function in_array;
use function is_array;
use function is_int;

/**
 * BaseListView is an abstract base class for widgets that display data items in terms of a list or grid.
 *
 * It provides essential features for displaying data from a data reader, including:
 *
 * - Pagination: Both offset-based and keyset-based pagination are supported
 * - Sorting: Configurable sorting of data with single or multiple column support
 * - Filtering: Ability to filter data based on various criteria
 * - Page Size Control: Customizable page size with various constraints
 * - Summary Information: Configurable summary showing pagination details
 * - Empty Data Handling: Customizable display when no data is available
 * - Internationalization: Built-in translation support
 * - URL Management: Flexible URL creation for pagination, sorting, and filtering
 *
 * Features:
 *
 * 1. Data Source:
 *    - Uses {@see ReadableDataInterface} as the data source
 *    - Supports various data reader types: Countable, Filterable, Limitable, Offsetable, Sortable
 *    - Automatically adapts to the capabilities of the provided data reader
 *
 * 2. Pagination:
 *    - Supports both offset-based ({@see OffsetPaginator}) and keyset-based ({@see KeysetPaginator}) pagination
 *    - Customizable page size with constraints (fixed, range, or unlimited)
 *    - Page size can be controlled via URL parameters
 *    - Handles missing pages gracefully with configurable behavior
 *
 * 3. Layout and Rendering:
 *    - Customizable layout template with sections for header, toolbar, items, summary, pager, and page size
 *    - Configurable HTML containers and attributes for each section
 *    - Support for custom widgets for pagination and page size controls
 *    - Flexible empty data handling with customizable messages
 *
 * 4. URL Configuration:
 *    - Customizable URL creation for pagination, sorting, and filtering
 *    - Support for both path parameters and query parameters
 *    - Configurable parameter names for page, page size, and sort
 *
 * 5. Internationalization:
 *    - Built-in translation support via {@see TranslatorInterface}
 *    - Configurable translation category
 *    - Support for both simple and Intl message formatting
 *
 * Example usage:
 * ```php
 * // Create a list view with a data reader
 * $listView = ListView::widget()
 *     ->dataReader($dataReader)
 *     ->pageSize(20)
 *     ->layout("{items}\n{pager}")
 *     ->urlCreator(fn ($args) => Url::to(['list', ...$args]))
 *     ->render();
 *
 * // Create a grid view with sorting and filtering
 * $gridView = GridView::widget()
 *     ->dataReader($dataReader)
 *     ->columns([
 *         'id',
 *         'name',
 *         'email',
 *     ])
 *     ->enableMultiSort()
 *     ->render();
 * ```
 *
 * @psalm-type UrlArguments = array<string,scalar|Stringable|null>
 * @psalm-type UrlCreator = callable(UrlArguments,array):string
 * @psalm-type PageNotFoundExceptionCallback = callable(PageNotFoundException):void
 * @psalm-type PageSizeConstraint = list<positive-int>|positive-int|bool
 * @psalm-import-type TOrder from Sort
 */
abstract class BaseListView extends Widget
{
    /**
     * @psalm-var UrlCreator|null
     */
    protected $urlCreator = null;
    protected UrlConfig $urlConfig;

    /**
     * @var int Page size that is used in case it is not set explicitly.
     * @psalm-var positive-int
     */
    protected int $defaultPageSize = PaginatorInterface::DEFAULT_PAGE_SIZE;

    /**
     * @var array|bool|int Page size constraint.
     *  - `true` - default only.
     *  - `false` - no constraint.
     *  - int - maximum page size.
     *  -  [int, int, ...] - a list of page sizes to choose from.
     *
     * @see PageSizeContext::FIXED_VALUE
     * @see PageSizeContext::ANY_VALUE
     *
     * @psalm-var PageSizeConstraint
     */
    protected bool|int|array $pageSizeConstraint = true;

    /**
     * @psalm-var non-empty-string|null
     */
    private ?string $pageSizeTag = 'div';
    private array $pageSizeAttributes = [];
    private ?string $pageSizeTemplate = 'Results per page {widget}';
    private PageSizeWidgetInterface|null $pageSizeWidget = null;

    /**
     * A name for {@see CategorySource} used with translator ({@see TranslatorInterface}) by default.
     * @psalm-suppress MissingClassConstType
     */
    final public const DEFAULT_TRANSLATION_CATEGORY = 'yii-dataview';

    /**
     * @var TranslatorInterface A translator instance used for translations of messages. If it wasn't set
     * explicitly in the constructor, a default one created automatically in {@see createDefaultTranslator()}.
     */
    protected readonly TranslatorInterface $translator;

    /**
     * @psalm-var non-empty-string|null
     */
    private ?string $containerTag = 'div';
    private array $containerAttributes = [];

    /**
     * @psalm-var non-empty-string|null
     */
    private ?string $summaryTag = 'div';
    private array $summaryAttributes = [];
    private ?string $summaryTemplate = 'Page <b>{currentPage}</b> of <b>{totalPages}</b>';

    protected ?string $emptyText = null;
    private array $emptyTextAttributes = [];
    private string $header = '';
    private array $headerAttributes = [];
    private string $layout = "{header}\n{toolbar}\n{items}\n{summary}\n{pager}\n{pageSize}";

    private array $offsetPaginationConfig = [];
    private array $keysetPaginationConfig = [];
    private PaginationWidgetInterface|null $paginationWidget = null;
    protected ?ReadableDataInterface $dataReader = null;
    private string $toolbar = '';

    /**
     * @psalm-var array<string,scalar|Stringable|null>
     */
    protected array $urlArguments = [];
    protected array $urlQueryParameters = [];

    protected UrlParameterProviderInterface|null $urlParameterProvider = null;

    private bool $ignoreMissingPage = true;
    protected bool $enableMultiSort = false;

    /**
     * @psalm-var PageNotFoundExceptionCallback|null
     */
    private $pageNotFoundExceptionCallback = null;

    protected ?ReadableDataInterface $preparedDataReader = null;

    public function __construct(
        TranslatorInterface|null $translator = null,
        protected readonly string $translationCategory = self::DEFAULT_TRANSLATION_CATEGORY,
    ) {
        $this->translator = $translator ?? $this->createDefaultTranslator();
        $this->urlConfig = new UrlConfig();
    }

    /**
     * @psalm-param UrlCreator|null $urlCreator
     */
    final public function urlCreator(?callable $urlCreator): static
    {
        $new = clone $this;
        $new->urlCreator = $urlCreator;
        return $new;
    }

    final public function ignoreMissingPage(bool $enabled): static
    {
        $new = clone $this;
        $new->ignoreMissingPage = $enabled;
        return $new;
    }

    /**
     * @psalm-param PageNotFoundExceptionCallback|null $callback
     */
    final public function pageNotFoundExceptionCallback(?callable $callback): static
    {
        $new = clone $this;
        $new->pageNotFoundExceptionCallback = $callback;
        return $new;
    }

    /**
     * Return a new instance with the name of argument or query parameter for page.
     *
     * @param string $name The name of argument or query parameter for page.
     */
    final public function pageParameterName(string $name): static
    {
        $new = clone $this;
        $new->urlConfig = $this->urlConfig->withPageParameterName($name);
        return $new;
    }

    final public function previousPageParameterName(string $name): static
    {
        $new = clone $this;
        $new->urlConfig = $this->urlConfig->withPreviousPageParameterName($name);
        return $new;
    }

    /**
     * Return a new instance with the name of argument or query parameter for page size.
     *
     * @param string $name The name of argument or query parameter for page size.
     */
    final public function pageSizeParameterName(string $name): static
    {
        $new = clone $this;
        $new->urlConfig = $this->urlConfig->withPageSizeParameterName($name);
        return $new;
    }

    final public function urlParameterProvider(?UrlParameterProviderInterface $provider): static
    {
        $new = clone $this;
        $new->urlParameterProvider = $provider;
        return $new;
    }

    final public function enableMultiSort(bool $enable = true): self
    {
        $new = clone $this;
        $new->enableMultiSort = $enable;
        return $new;
    }

    /**
     * Renders the data models.
     *
     * @return string The rendering result.
     *
     * @psalm-param array<array-key, array|object> $items
     */
    abstract protected function renderItems(array $items, ValidationResult $filterValidationResult): string;

    final public function containerTag(?string $tag): static
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->containerTag = $tag;
        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for container.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    final public function containerAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->containerAttributes = $attributes;
        return $new;
    }

    /**
     * Return a new instance with the empty text.
     *
     * @param ?string $emptyText The HTML content to be displayed when {@see dataProvider} doesn't have any data.
     *
     * The default value is the text "No results found." which will be translated to the current application language.
     *
     * {@see notShowOnEmpty()}
     * {@see emptyTextAttributes()}
     */
    public function emptyText(?string $emptyText): static
    {
        $new = clone $this;
        $new->emptyText = $emptyText;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the empty text.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    public function emptyTextAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->emptyTextAttributes = $attributes;

        return $new;
    }

    public function getDataReader(): ReadableDataInterface
    {
        if ($this->dataReader === null) {
            throw new DataReaderNotSetException();
        }

        return $this->dataReader;
    }

    /**
     * @return array The array with format:
     * ```
     * [
     *   FilterInterface[]|null, // Array of filters or `null` if there are definitely no entries for the current filter
     *   ValidationResult, // Validation result of the filter
     * ]
     * ```
     *
     * @psalm-return list{FilterInterface[]|null,ValidationResult}
     */
    protected function makeFilters(): array
    {
        return [[], new ValidationResult()];
    }

    /**
     * @param FilterInterface[] $filters
     *
     * @throws PageNotFoundException
     *
     * @psalm-return array<array-key, array|object>
     */
    private function prepareDataReaderAndGetItems(array $filters): array
    {
        $page = $this->urlParameterProvider?->get(
            $this->urlConfig->getPageParameterName(),
            $this->urlConfig->getPageParameterType()
        );
        $previousPage = $this->urlParameterProvider?->get(
            $this->urlConfig->getPreviousPageParameterName(),
            $this->urlConfig->getPreviousPageParameterType(),
        );
        $pageSize = $this->urlParameterProvider?->get(
            $this->urlConfig->getPageSizeParameterName(),
            $this->urlConfig->getPageSizeParameterType(),
        );
        $sort = $this->urlParameterProvider?->get(
            $this->urlConfig->getSortParameterName(),
            $this->urlConfig->getSortParameterType(),
        );

        $this->preparedDataReader = $this->prepareDataReaderByParams($page, $previousPage, $pageSize, $sort, $filters);

        try {
            return $this->getItems($this->preparedDataReader);
        } catch (PageNotFoundException $exception) {
        }

        if ($this->ignoreMissingPage) {
            $this->preparedDataReader = $this->prepareDataReaderByParams(null, null, $pageSize, $sort, $filters);
            try {
                return $this->getItems($this->preparedDataReader);
            } catch (PageNotFoundException $exception) {
            }
        }

        if ($this->pageNotFoundExceptionCallback !== null) {
            ($this->pageNotFoundExceptionCallback)($exception);
        }

        throw $exception;
    }

    /**
     * @throws PageNotFoundException
     *
     * @psalm-return array<array-key, array|object>
     */
    private function getItems(ReadableDataInterface $dataReader): array
    {
        $items = $dataReader->read();
        return is_array($items) ? $items : iterator_to_array($items);
    }

    /**
     * @param FilterInterface[] $filters
     */
    private function prepareDataReaderByParams(
        ?string $page,
        ?string $previousPage,
        ?string $pageSize,
        ?string $sort,
        array $filters,
    ): ReadableDataInterface {
        $dataReader = $this->getDataReader();

        if (!$dataReader instanceof PaginatorInterface) {
            if (
                $dataReader instanceof OffsetableDataInterface
                && $dataReader instanceof CountableDataInterface
                && $dataReader instanceof LimitableDataInterface
            ) {
                $dataReader = new OffsetPaginator($dataReader);
            } elseif (
                $dataReader instanceof FilterableDataInterface
                && $dataReader instanceof SortableDataInterface
                && $dataReader instanceof LimitableDataInterface
            ) {
                if ($dataReader->getSort() !== null) {
                    $dataReader = new KeysetPaginator($dataReader);
                } else {
                    return $dataReader;
                }
            } else {
                return $dataReader;
            }
        }

        if ($dataReader->isPaginationRequired()) {
            $dataReader = $dataReader->withPageSize(
                $this->preparePageSize($pageSize) ?? $this->getDefaultPageSize()
            );

            if ($page !== null) {
                $dataReader = $dataReader->withToken(PageToken::next($page));
            } elseif ($previousPage !== null) {
                $dataReader = $dataReader->withToken(PageToken::previous($previousPage));
            }
        }

        if (!empty($sort) && $dataReader->isSortable()) {
            $sortObject = $dataReader->getSort();
            if ($sortObject !== null) {
                $order = OrderHelper::stringToArray($sort);
                if (!$this->enableMultiSort) {
                    $order = array_slice($order, 0, 1, true);
                }
                $this->prepareOrder($order);
                $dataReader = $dataReader->withSort($sortObject->withOrder($order));
            }
        }

        if (!empty($filters) && $dataReader->isFilterable()) {
            $dataReader = $dataReader->withFilter(new All(...$filters));
        }

        return $dataReader;
    }

    /**
     * @psalm-param TOrder $order
     */
    protected function prepareOrder(array &$order): void
    {
    }

    /**
     * Return new instance with the header for the grid.
     *
     * @param string $content The header of the grid.
     *
     * {@see headerAttributes}
     */
    public function header(string $content): self
    {
        $new = clone $this;
        $new->header = $content;

        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the header.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    public function headerAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->headerAttributes = $attributes;

        return $new;
    }

    /**
     * Returns a new instance with the id of the grid view, detail view, or list view.
     *
     * @param string $id The ID of the grid view, detail view, or list view.
     */
    public function id(string $id): static
    {
        $new = clone $this;
        $new->containerAttributes['id'] = $id;
        return $new;
    }

    /**
     * Returns a new instance with the layout of the grid view, and list view.
     *
     * @param string $view The template that determines how different sections of the grid view, list view. Should be
     * organized.
     *
     * The following tokens will be replaced with the corresponding section contents:
     *
     * - `{header}`: The header section.
     * - `{toolbar}`: The toolbar section.
     */
    public function layout(string $view): static
    {
        $new = clone $this;
        $new->layout = $view;

        return $new;
    }

    final public function pageSizeTag(?string $tag): static
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->pageSizeTag = $tag;
        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for page size wrapper tag.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    final public function pageSizeAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->pageSizeAttributes = $attributes;
        return $new;
    }

    /**
     * Returns a new instance with the page size template.
     *
     * @param string|null $template The HTML content to be displayed as the page size control. If you don't want to show
     * control, you may set it with an empty string or null.
     *
     * The following tokens will be replaced with the corresponding values:
     *
     * - `{widget}` — page size widget.
     */
    final public function pageSizeTemplate(?string $template): static
    {
        $new = clone $this;
        $new->pageSizeTemplate = $template;
        return $new;
    }

    final public function pageSizeWidget(?PageSizeWidgetInterface $widget): static
    {
        $new = clone $this;
        $new->pageSizeWidget = $widget;
        return $new;
    }

    public function paginationWidget(PaginationWidgetInterface|null $widget): static
    {
        $new = clone $this;
        $new->paginationWidget = $widget;
        return $new;
    }

    /**
     * Set configuration for offset pagination widget.
     *
     * @param array $config Widget config.
     * @return $this
     */
    public function offsetPaginationConfig(array $config): static
    {
        $new = clone $this;
        $new->offsetPaginationConfig = $config;
        return $new;
    }

    /**
     * Set configuration for keyset pagination widget.
     *
     * @param array $config Widget config.
     * @return $this
     */
    public function keysetPaginationConfig(array $config): static
    {
        $new = clone $this;
        $new->keysetPaginationConfig = $config;
        return $new;
    }

    /**
     * Returns a new instance with the paginator interface of the grid view, detail view, or list view.
     *
     * @param ReadableDataInterface $dataReader The paginator interface of the grid view, detail view, or list view.
     */
    public function dataReader(ReadableDataInterface $dataReader): static
    {
        $new = clone $this;
        $new->dataReader = $dataReader;
        return $new;
    }

    final public function summaryTag(?string $tag): static
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->summaryTag = $tag;
        return $new;
    }

    /**
     * Returns a new instance with the summary template.
     *
     * @param string|null $template The HTML content to be displayed as the summary. If you don't want to show
     * the summary, you may set it with an empty string or null.
     *
     * The following tokens will be replaced with the corresponding values:
     *
     * - `{begin}` — the starting row number (1-based) currently being displayed.
     * - `{end}` — the ending row number (1-based) currently being displayed.
     * - `{count}` — the number of rows currently being displayed.
     * - `{totalCount}` — the total number of rows available.
     * - `{currentPage}` — the page number (1-based) current being displayed.
     * - `{totalPages}` — the number of pages available.
     */
    final public function summaryTemplate(?string $template): static
    {
        $new = clone $this;
        $new->summaryTemplate = $template;
        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for summary wrapper tag.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    final public function summaryAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->summaryAttributes = $attributes;
        return $new;
    }

    /**
     * Return new instance with toolbar content.
     *
     * @param string $content The toolbar content.
     *
     * @psalm-param array $toolbar
     */
    public function toolbar(string $content): self
    {
        $new = clone $this;
        $new->toolbar = $content;

        return $new;
    }

    /**
     * Return a new instance with arguments of the route.
     *
     * @param array $arguments Arguments of the route.
     *
     * @psalm-param array<string,scalar|Stringable|null> $arguments
     */
    public function urlArguments(array $arguments): static
    {
        $new = clone $this;
        $new->urlArguments = $arguments;
        return $new;
    }

    /**
     * Return a new instance with query parameters of the route.
     *
     * @param array $parameters The query parameters of the route.
     */
    public function urlQueryParameters(array $parameters): static
    {
        $new = clone $this;
        $new->urlQueryParameters = $parameters;

        return $new;
    }

    protected function renderEmpty(int $colspan): Td
    {
        $emptyTextAttributes = $this->emptyTextAttributes;
        $emptyTextAttributes['colspan'] = $colspan;

        $emptyText = $this->translator->translate(
            $this->emptyText ?? 'No results found.',
            category: $this->translationCategory
        );

        return Td::tag()->attributes($emptyTextAttributes)->content($emptyText);
    }

    public function render(): string
    {
        [$filters, $filterValidationResult] = $this->makeFilters();
        $items = $filters === null ? [] : $this->prepareDataReaderAndGetItems($filters);

        $content = trim(
            strtr(
                $this->layout,
                [
                    '{header}' => $this->renderHeader(),
                    '{toolbar}' => $this->toolbar,
                    '{items}' => $this->renderItems($items, $filterValidationResult),
                    '{summary}' => $this->renderSummary(),
                    '{pager}' => $this->renderPagination(),
                    '{pageSize}' => $this->renderPageSize(),
                ],
            )
        );

        return $this->containerTag === null
            ? $content
            : Html::tag($this->containerTag, "\n" . $content . "\n", $this->containerAttributes)
                ->encode(false)
                ->render();
    }

    /**
     * @psalm-return positive-int
     */
    protected function getDefaultPageSize(): int
    {
        $dataReader = $this->getDataReader();
        $pageSize = $dataReader instanceof PaginatorInterface
            ? $dataReader->getPageSize()
            : $this->defaultPageSize;

        if (is_int($this->pageSizeConstraint)) {
            return $pageSize <= $this->pageSizeConstraint
                ? $pageSize
                : $this->pageSizeConstraint;
        }

        if (is_array($this->pageSizeConstraint)) {
            return in_array($pageSize, $this->pageSizeConstraint, true)
                ? $pageSize
                : $this->pageSizeConstraint[0];
        }

        return $pageSize;
    }

    /**
     * Get a new instance with a page size constraint set.
     *
     * @param array|bool|int $pageSizeConstraint Page size constraint.
     * `true` - default only.
     * `false` - no constraint.
     * int - maximum page size.
     * [int, int, ...] - a list of page sizes to choose from.
     * @return static New instance.
     *
     * @psalm-param PageSizeConstraint $pageSizeConstraint
     */
    public function pageSizeConstraint(array|int|bool $pageSizeConstraint): static
    {
        $new = clone $this;
        $new->pageSizeConstraint = $pageSizeConstraint;
        return $new;
    }

    /**
     * Returns order field names that should be replaced in URL sort argument.
     * Format: `['field_name_in_url' => 'real_field_name']`.
     *
     * @psalm-return array<string, string>
     */
    protected function getOverrideOrderFields(): array
    {
        return [];
    }

    private function renderPagination(): string
    {
        $dataReader = $this->preparedDataReader;
        if (!$dataReader instanceof PaginatorInterface || !$dataReader->isPaginationRequired()) {
            return '';
        }

        if ($this->paginationWidget === null) {
            if ($dataReader instanceof OffsetPaginator) {
                $widget = OffsetPagination::widget(config: $this->offsetPaginationConfig);
            } elseif ($dataReader instanceof KeysetPaginator) {
                $widget = KeysetPagination::widget(config: $this->keysetPaginationConfig);
            } else {
                return '';
            }
            try {
                $widget = $widget->withPaginator($dataReader);
            } catch (PaginatorNotSupportedException) {
                return '';
            }
        } else {
            $widget = $this->paginationWidget->withPaginator($dataReader);
        }

        if ($this->urlCreator === null) {
            $nextUrlPattern = '#page=' . PaginationContext::URL_PLACEHOLDER;
            $previousUrlPattern = '#previous-page=' . PaginationContext::URL_PLACEHOLDER;
            $defaultUrl = '#';
        } else {
            $pageSize = $this->getPageSizeValueForUrl($dataReader);
            $sort = $this->getSortValueForUrl($dataReader);
            $nextUrlPattern = call_user_func_array(
                $this->urlCreator,
                UrlParametersFactory::create(
                    PageToken::next(PaginationContext::URL_PLACEHOLDER),
                    $pageSize,
                    $sort,
                    $this->urlConfig
                ),
            );
            $previousUrlPattern = call_user_func_array(
                $this->urlCreator,
                UrlParametersFactory::create(
                    PageToken::previous(PaginationContext::URL_PLACEHOLDER),
                    $pageSize,
                    $sort,
                    $this->urlConfig
                ),
            );
            $defaultUrl = call_user_func_array(
                $this->urlCreator,
                UrlParametersFactory::create(null, $pageSize, $sort, $this->urlConfig),
            );
        }

        $context = new PaginationContext(
            $this->getOverrideOrderFields(),
            $nextUrlPattern,
            $previousUrlPattern,
            $defaultUrl,
        );

        return $widget->withContext($context)->render();
    }

    private function renderPageSize(): string
    {
        if (empty($this->pageSizeTemplate)) {
            return '';
        }

        $dataReader = $this->preparedDataReader;
        if (!$dataReader instanceof PaginatorInterface) {
            return '';
        }

        if ($this->pageSizeWidget === null) {
            if ($this->pageSizeConstraint === false || is_int($this->pageSizeConstraint)) {
                $widget = InputPageSize::widget();
            } elseif (is_array($this->pageSizeConstraint)) {
                $widget = SelectPageSize::widget();
            } else {
                return '';
            }
        } else {
            $widget = $this->pageSizeWidget;
        }

        if ($this->urlCreator === null) {
            $urlPattern = '#pagesize=' . PageSizeContext::URL_PLACEHOLDER;
            $defaultUrl = '#';
        } else {
            $sort = $this->getSortValueForUrl($dataReader);
            $urlPattern = call_user_func_array(
                $this->urlCreator,
                UrlParametersFactory::create(
                    null,
                    PageSizeContext::URL_PLACEHOLDER,
                    $sort,
                    $this->urlConfig
                ),
            );
            $defaultUrl = call_user_func_array(
                $this->urlCreator,
                UrlParametersFactory::create(null, null, $sort, $this->urlConfig),
            );
        }

        $context = new PageSizeContext(
            $dataReader->getPageSize(),
            $this->getDefaultPageSize(),
            $this->pageSizeConstraint,
            $urlPattern,
            $defaultUrl,
        );
        $renderedWidget = $widget->withContext($context)->render();

        $content = $this->translator->translate(
            $this->pageSizeTemplate,
            ['widget' => $renderedWidget],
            $this->translationCategory,
        );

        return $this->pageSizeTag === null
            ? $content
            : Html::tag($this->pageSizeTag, $content, $this->pageSizeAttributes)->encode(false)->render();
    }

    private function renderSummary(): string
    {
        if (empty($this->summaryTemplate)) {
            return '';
        }

        $dataReader = $this->preparedDataReader;
        if (!$dataReader instanceof OffsetPaginator) {
            return '';
        }

        // The total number of rows available
        $totalCount = $dataReader->getTotalItems();
        if ($totalCount === 0) {
            return '';
        }

        // The page number (1-based) current being displayed
        $currentPage = $dataReader->getCurrentPage();

        // The starting row number (1-based) currently being displayed
        $begin = ($currentPage - 1) * $dataReader->getPageSize() + 1;

        // The number of rows currently being displayed
        $count = $dataReader->getCurrentPageSize();

        // The ending row number (1-based) currently being displayed
        $end = $begin + $count - 1;

        // The number of pages available
        $totalPages = $dataReader->getTotalPages();

        $content = $this->translator->translate(
            $this->summaryTemplate,
            [
                'begin' => $begin,
                'end' => $end,
                'count' => $count,
                'totalCount' => $totalCount,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
            ],
            $this->translationCategory,
        );

        return $this->summaryTag === null
            ? $content
            : Html::tag($this->summaryTag, $content, $this->summaryAttributes)->encode(false)->render();
    }

    private function renderHeader(): string
    {
        return match ($this->header) {
            '' => '',
            default => Div::tag()
                ->attributes($this->headerAttributes)
                ->content($this->header)
                ->encode(false)
                ->render(),
        };
    }

    /**
     * @psalm-return positive-int|null
     */
    private function preparePageSize(?string $rawPageSize): ?int
    {
        if ($this->pageSizeConstraint === true) {
            return null;
        }

        if ($rawPageSize === null) {
            return null;
        }

        $pageSize = (int) $rawPageSize;
        if ($pageSize < 1) {
            return null;
        }

        if ($this->pageSizeConstraint === false) {
            return $pageSize;
        }

        if (is_int($this->pageSizeConstraint) && $pageSize <= $this->pageSizeConstraint) {
            return $pageSize;
        }

        if (is_array($this->pageSizeConstraint) && in_array($pageSize, $this->pageSizeConstraint, true)) {
            return $pageSize;
        }

        return null;
    }

    /**
     * Creates default translator to use if {@see $translator} wasn't set explicitly in the constructor. Depending on
     * "intl" extension availability, either {@see IntlMessageFormatter} or {@see SimpleMessageFormatter} is used as
     * formatter.
     *
     * @return Translator Translator instance used for translations of messages.
     */
    private function createDefaultTranslator(): Translator
    {
        $categorySource = new CategorySource(
            $this->translationCategory,
            new IdMessageReader(),
            extension_loaded('intl') ? new IntlMessageFormatter() : new SimpleMessageFormatter(),
        );
        $translator = new Translator();
        $translator->addCategorySources($categorySource);

        return $translator;
    }

    private function getPageSizeValueForUrl(PaginatorInterface $paginator): ?string
    {
        $pageSize = $paginator->getPageSize();
        return $pageSize === $this->getDefaultPageSize()
            ? null
            : (string) $pageSize;
    }

    private function getSortValueForUrl(PaginatorInterface $paginator): ?string
    {
        $sort = $this->getSort($paginator);
        if ($sort === null) {
            return null;
        }

        $originalSort = $this->getSort($this->dataReader);
        if ($originalSort?->getOrderAsString() === $sort->getOrderAsString()) {
            return null;
        }

        $order = [];
        $overrideOrderFields = array_flip($this->getOverrideOrderFields());
        foreach ($sort->getOrder() as $name => $value) {
            $key = array_key_exists($name, $overrideOrderFields)
                ? $overrideOrderFields[$name]
                : $name;
            $order[$key] = $value;
        }

        return OrderHelper::arrayToString($order);
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
