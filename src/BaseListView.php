<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use BackedEnum;
use InvalidArgumentException;
use Stringable;
use Yiisoft\Data\Paginator\InvalidPageException;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PageNotFoundException;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Data\Reader\CountableDataInterface;
use Yiisoft\Data\Reader\Filter\AndX;
use Yiisoft\Data\Reader\FilterableDataInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\LimitableDataInterface;
use Yiisoft\Data\Reader\OffsetableDataInterface;
use Yiisoft\Data\Reader\OrderHelper;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Data\Reader\SortableDataInterface;
use Yiisoft\Html\Html;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IdMessageReader;
use Yiisoft\Translator\IntlMessageFormatter;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result as ValidationResult;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\ListView\ListView;
use Yiisoft\Yii\DataView\PageSize\InputPageSize;
use Yiisoft\Yii\DataView\PageSize\PageSizeContext;
use Yiisoft\Yii\DataView\PageSize\PageSizeWidgetInterface;
use Yiisoft\Yii\DataView\PageSize\SelectPageSize;
use Yiisoft\Yii\DataView\Pagination\KeysetPagination;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\DataView\Pagination\PaginationContext;
use Yiisoft\Yii\DataView\Pagination\PaginationWidgetInterface;
use Yiisoft\Yii\DataView\Url\NullUrlParameterProvider;
use Yiisoft\Yii\DataView\Url\UrlConfig;
use Yiisoft\Yii\DataView\Url\UrlParameterProviderInterface;
use Yiisoft\Yii\DataView\Url\UrlParametersFactory;
use Yiisoft\Yii\DataView\Url\UrlParameterType;

use function array_slice;
use function call_user_func_array;
use function extension_loaded;
use function in_array;
use function is_array;
use function is_int;

/**
 * `BaseListView` is an abstract base class for {@see GridView} and {@see ListView} only.
 *
 * @psalm-type UrlArguments = array<string,scalar|Stringable|null>
 * @psalm-type UrlCreator = callable(UrlArguments,array):string
 * @psalm-type PageSizeConstraint = list<positive-int>|positive-int|bool
 * @psalm-type PageNotFoundExceptionCallback = callable(InvalidPageException):void
 * @psalm-import-type TOrder from Sort
 */
abstract class BaseListView extends Widget
{
    /**
     * A name for {@see CategorySource} used with translator ({@see TranslatorInterface}) by default.
     * @psalm-suppress MissingClassConstType
     */
    final public const DEFAULT_TRANSLATION_CATEGORY = 'yii-dataview';

    private ReadableDataInterface|null $dataReader = null;

    /**
     * @psalm-var UrlCreator|null
     */
    protected $urlCreator = null;
    protected UrlConfig $urlConfig;
    protected UrlParameterProviderInterface $urlParameterProvider;

    /**
     * @var array|bool|int Page size constraint.
     *  - `true` - default only.
     *  - `false` - no constraint.
     *  - int - maximum page size.
     *  - [int, int, ...] - a list of page sizes to choose from.
     *
     * @see PageSizeContext::FIXED_VALUE
     * @see PageSizeContext::ANY_VALUE
     *
     * @psalm-var PageSizeConstraint
     */
    private bool|int|array $pageSizeConstraint = true;

    protected bool $multiSort = false;

    private bool $ignoreMissingPage = true;

    /**
     * @psalm-var PageNotFoundExceptionCallback|null
     */
    private $pageNotFoundExceptionCallback = null;

    /** @psalm-var non-empty-string|null */
    private ?string $containerTag = 'div';
    private array $containerAttributes = [];
    private string $prepend = '';
    private string $append = '';

    private string $layout = "{header}\n{toolbar}\n{items}\n{summary}\n{pager}\n{pageSize}";

    /** @psalm-var non-empty-string|null */
    private ?string $headerTag = 'div';
    private string $header = '';
    private array $headerAttributes = [];
    private bool $encodeHeader = true;

    private string $toolbar = '';

    /**
     * @psalm-var non-empty-string|null
     */
    private string|null $summaryTag = 'div';
    private array $summaryAttributes = [];
    private string|null $summaryTemplate = 'Page <b>{currentPage}</b> of <b>{totalPages}</b>';

    private PaginationWidgetInterface|null $paginationWidget = null;
    private array $offsetPaginationConfig = [];
    private array $keysetPaginationConfig = [];

    private PageSizeWidgetInterface|null $pageSizeWidget = null;
    /**
     * @psalm-var non-empty-string|null
     */
    private string|null $pageSizeTag = 'div';
    private array $pageSizeAttributes = [];
    private string|null $pageSizeTemplate = 'Results per page {widget}';

    private string $noResultsText = 'No results found.';
    private string $noResultsTemplate = '{text}';

    /**
     * @var TranslatorInterface A translator instance used for translations of messages. If it wasn't set
     * explicitly in the constructor, a default one created automatically in {@see createDefaultTranslator()}.
     */
    protected readonly TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface|null $translator = null,
        protected readonly string $translationCategory = self::DEFAULT_TRANSLATION_CATEGORY,
    ) {
        $this->translator = $translator ?? $this->createDefaultTranslator();
        $this->urlConfig = new UrlConfig();
        $this->urlParameterProvider = new NullUrlParameterProvider();
    }

    final public function render(): string
    {
        [$filters, $filterValidationResult] = $this->makeFilters();
        [$preparedDataReader, $items] = $filters === null
            ? [null, []]
            : $this->prepareDataReaderAndItems($filters);

        $content = trim(
            strtr(
                $this->layout,
                [
                    '{header}' => $this->renderHeader(),
                    '{toolbar}' => $this->toolbar,
                    '{items}' => $this->renderItems($items, $filterValidationResult, $preparedDataReader),
                    '{summary}' => $this->renderSummary($preparedDataReader),
                    '{pager}' => $this->renderPagination($preparedDataReader),
                    '{pageSize}' => $this->renderPageSize($preparedDataReader),
                ],
            )
        );

        if ($this->prepend !== '') {
            $content = $this->prepend . "\n" . $content;
        }
        if ($this->append !== '') {
            $content .= "\n" . $this->append;
        }

        return $this->containerTag === null
            ? $content
            : Html::tag($this->containerTag, "\n" . $content . "\n", $this->containerAttributes)
                ->encode(false)
                ->render();
    }

    /**
     * @template TKey as array-key
     * @template TValue as array|object
     * @psalm-param ReadableDataInterface<TKey,TValue> $dataReader
     */
    final public function dataReader(ReadableDataInterface $dataReader): static
    {
        $new = clone $this;
        $new->dataReader = $dataReader;
        return $new;
    }

    /**
     * @psalm-param UrlCreator|null $urlCreator
     */
    final public function urlCreator(callable|null $urlCreator): static
    {
        $new = clone $this;
        $new->urlCreator = $urlCreator;
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

    /**
     * Creates a new instance with the specified sort parameter name.
     *
     * @param string $name The new sort parameter name.
     *
     * @return static A new instance with the updated sort parameter name.
     */
    final public function sortParameterName(string $name): static
    {
        $new = clone $this;
        $new->urlConfig = $this->urlConfig->withSortParameterName($name);
        return $new;
    }

    /**
     * Creates a new instance with the specified page parameter type.
     *
     * @param int $type The new page parameter type. Must be one of:
     *  - `UrlParameterType::PATH` for path parameters
     *  - `UrlParameterType::QUERY` for query parameters
     *
     * @return static A new instance with the updated page parameter type.
     *
     * @psalm-param UrlParameterType::* $type
     */
    final public function pageParameterType(int $type): static
    {
        $new = clone $this;
        $new->urlConfig = $this->urlConfig->withPageParameterType($type);
        return $new;
    }

    /**
     * Creates a new instance with the specified previous page parameter type.
     *
     * @param int $type The new previous page parameter type. Must be one of:
     *  - `UrlParameterType::PATH` for path parameters
     *  - `UrlParameterType::QUERY` for query parameters
     *
     * @return static A new instance with the updated previous page parameter type.
     *
     * @psalm-param UrlParameterType::* $type
     */
    final public function previousPageParameterType(int $type): static
    {
        $new = clone $this;
        $new->urlConfig = $this->urlConfig->withPreviousPageParameterType($type);
        return $new;
    }

    /**
     * Creates a new instance with the specified page size parameter type.
     *
     * @param int $type The new page size parameter type. Must be one of:
     *  - `UrlParameterType::PATH` for path parameters
     *  - `UrlParameterType::QUERY` for query parameters
     *
     * @return static A new instance with the updated page size parameter type.
     *
     * @psalm-param UrlParameterType::* $type
     */
    final public function pageSizeParameterType(int $type): static
    {
        $new = clone $this;
        $new->urlConfig = $this->urlConfig->withPageSizeParameterType($type);
        return $new;
    }

    /**
     * Creates a new instance with the specified sort parameter type.
     *
     * @param int $type The new sort parameter type. Must be one of:
     *  - `UrlParameterType::PATH` for path parameters
     *  - `UrlParameterType::QUERY` for query parameters
     *
     * @return static A new instance with the updated sort parameter type.
     *
     * @psalm-param UrlParameterType::* $type
     */
    final public function sortParameterType(int $type): static
    {
        $new = clone $this;
        $new->urlConfig = $this->urlConfig->withSortParameterType($type);
        return $new;
    }

    /**
     * Return a new instance with arguments of the route.
     *
     * @param array $arguments Arguments of the route.
     *
     * @psalm-param array<string,scalar|Stringable|null> $arguments
     */
    final public function urlArguments(array $arguments): static
    {
        $new = clone $this;
        $new->urlConfig = $this->urlConfig->withArguments($arguments);
        return $new;
    }

    /**
     * Return a new instance with query parameters of the route.
     *
     * @param array $parameters The query parameters of the route.
     */
    final public function urlQueryParameters(array $parameters): static
    {
        $new = clone $this;
        $new->urlConfig = $this->urlConfig->withQueryParameters($parameters);
        return $new;
    }

    final public function urlParameterProvider(UrlParameterProviderInterface $provider): static
    {
        $new = clone $this;
        $new->urlParameterProvider = $provider;
        return $new;
    }

    /**
     * Get a new instance with a page size constraint set.
     *
     * @param array|bool|int $pageSizeConstraint Page size constraint.
     * `true` - default only.
     * `false` - no constraint.
     * `int` - maximum page size.
     * `[int, int, ...]` - a list of page sizes to choose from.
     * @return static New instance.
     *
     * @psalm-param PageSizeConstraint $pageSizeConstraint
     */
    final public function pageSizeConstraint(array|int|bool $pageSizeConstraint): static
    {
        $new = clone $this;
        $new->pageSizeConstraint = $pageSizeConstraint;
        return $new;
    }

    final public function multiSort(bool $enable = true): static
    {
        $new = clone $this;
        $new->multiSort = $enable;
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

    final public function containerTag(string|null $tag): static
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
     * Returns a new instance with the id of the grid view, detail view, or list view.
     *
     * @param string $id The ID of the grid view, detail view, or list view.
     */
    final public function id(string $id): static
    {
        $new = clone $this;
        $new->containerAttributes['id'] = $id;
        return $new;
    }

    /**
     * Set new container classes.
     *
     * Multiple classes can be set by passing them as separate arguments. `null` values are filtered out
     * automatically.
     *
     * @param BackedEnum|string|null ...$class One or more CSS class names to use. Pass `null` to skip a class.
     * @return static A new instance with the specified CSS classes set.
     */
    public function containerClass(BackedEnum|string|null ...$class): static
    {
        $new = clone $this;
        $new->containerAttributes['class'] = [];
        Html::addCssClass($new->containerAttributes, $class);
        return $new;
    }

    /**
     * Adds one or more CSS classes to the existing container classes.
     *
     * Multiple classes can be added by passing them as separate arguments. `null` values are filtered out
     * automatically.
     *
     * @param BackedEnum|string|null ...$class One or more CSS class names to add. Pass `null` to skip adding a class.
     * @return static A new instance with the specified CSS classes added to existing ones.
     */
    public function addContainerClass(BackedEnum|string|null ...$class): static
    {
        $new = clone $this;
        Html::addCssClass($new->containerAttributes, $class);
        return $new;
    }

    /**
     * Returns a new instance with HTML content to be added after the opening container tag.
     *
     * @param string|Stringable ...$prepend The HTML content to be prepended.
     */
    final public function prepend(string|Stringable ...$prepend): static
    {
        $new = clone $this;
        $new->prepend = implode('', $prepend);
        return $new;
    }

    /**
     * Returns a new instance with HTML content to be added before the closing container tag.
     *
     * @param string|Stringable ...$append The HTML content to be appended.
     */
    final public function append(string|Stringable ...$append): static
    {
        $new = clone $this;
        $new->append = implode('', $append);
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
    final public function layout(string $view): static
    {
        $new = clone $this;
        $new->layout = $view;
        return $new;
    }

    /**
     * Return new instance with the header for the grid.
     *
     * @param string $content The header of the grid.
     *
     * {@see headerAttributes}
     */
    final public function header(string $content): static
    {
        $new = clone $this;
        $new->header = $content;
        return $new;
    }

    final public function headerTag(string|null $tag): static
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->headerTag = $tag;
        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the header.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    final public function headerAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->headerAttributes = $attributes;
        return $new;
    }

    public function headerClass(BackedEnum|string|null ...$class): static
    {
        $new = clone $this;
        $new->headerAttributes['class'] = [];
        Html::addCssClass($new->headerAttributes, $class);
        return $new;
    }

    public function addHeaderClass(BackedEnum|string|null ...$class): static
    {
        $new = clone $this;
        Html::addCssClass($new->headerAttributes, $class);
        return $new;
    }

    final public function encodeHeader(bool $encode): static
    {
        $new = clone $this;
        $new->encodeHeader = $encode;
        return $new;
    }

    /**
     * Return new instance with toolbar content.
     *
     * @param string $content The toolbar content.
     *
     * @psalm-param array $toolbar
     */
    final public function toolbar(string $content): static
    {
        $new = clone $this;
        $new->toolbar = $content;
        return $new;
    }

    final public function summaryTag(string|null $tag): static
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->summaryTag = $tag;
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

    final public function paginationWidget(PaginationWidgetInterface|null $widget): static
    {
        $new = clone $this;
        $new->paginationWidget = $widget;
        return $new;
    }

    /**
     * Set configuration for offset pagination widget.
     *
     * @param array $config Widget config.
     */
    final public function offsetPaginationConfig(array $config): static
    {
        $new = clone $this;
        $new->offsetPaginationConfig = $config;
        return $new;
    }

    /**
     * Set configuration for keyset pagination widget.
     *
     * @param array $config Widget config.
     */
    final public function keysetPaginationConfig(array $config): static
    {
        $new = clone $this;
        $new->keysetPaginationConfig = $config;
        return $new;
    }

    final public function pageSizeWidget(PageSizeWidgetInterface|null $widget): static
    {
        $new = clone $this;
        $new->pageSizeWidget = $widget;
        return $new;
    }

    final public function pageSizeTag(string|null $tag): static
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
     * control, you may set it with an empty string or `null`.
     *
     * The following tokens will be replaced with the corresponding values:
     *
     * - `{widget}` — page size widget.
     */
    final public function pageSizeTemplate(string|null $template): static
    {
        $new = clone $this;
        $new->pageSizeTemplate = $template;
        return $new;
    }

    final public function noResultsText(string $text): static
    {
        $new = clone $this;
        $new->noResultsText = $text;
        return $new;
    }

    final public function noResultsTemplate(string $template): static
    {
        $new = clone $this;
        $new->noResultsTemplate = $template;
        return $new;
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
     * @psalm-return list{array<FilterInterface>|null, ValidationResult}
     */
    abstract protected function makeFilters(): array;

    /**
     * @psalm-param TOrder $order
     * @psalm-return TOrder
     */
    abstract protected function prepareOrder(array $order): array;

    /**
     * Renders the data models.
     *
     * @param array<array|object> $items The data models to be rendered.
     * @param ValidationResult $filterValidationResult The validation result of the filter.
     * @param ReadableDataInterface|null $preparedDataReader The prepared data reader, `null` means that there is no
     * data.
     *
     * @return string The rendering result.
     *
     * @psalm-param array<array|object> $items
     */
    abstract protected function renderItems(
        array $items,
        ValidationResult $filterValidationResult,
        ReadableDataInterface|null $preparedDataReader,
    ): string;

    final protected function getDataReader(): ReadableDataInterface
    {
        if ($this->dataReader === null) {
            throw new DataReaderNotSetException();
        }

        return $this->dataReader;
    }

    /**
     * @psalm-return positive-int
     */
    final protected function getDefaultPageSize(): int
    {
        $dataReader = $this->getDataReader();
        $pageSize = $dataReader instanceof PaginatorInterface
            ? $dataReader->getPageSize()
            : PaginatorInterface::DEFAULT_PAGE_SIZE;

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

    final protected function getNoResultsContent(): string
    {
        $text = $this->translator->translate(
            $this->noResultsText,
            category: $this->translationCategory,
        );

        return str_replace('{text}', $text, $this->noResultsTemplate);
    }

    /**
     * @param FilterInterface[] $filters
     *
     * @throws PageNotFoundException
     *
     * @psalm-return list{ReadableDataInterface|null, array<array|object>}
     */
    private function prepareDataReaderAndItems(array $filters): array
    {
        $page = $this->urlParameterProvider->get(
            $this->urlConfig->getPageParameterName(),
            $this->urlConfig->getPageParameterType()
        );
        $previousPage = $this->urlParameterProvider->get(
            $this->urlConfig->getPreviousPageParameterName(),
            $this->urlConfig->getPreviousPageParameterType(),
        );
        $pageSize = $this->urlParameterProvider->get(
            $this->urlConfig->getPageSizeParameterName(),
            $this->urlConfig->getPageSizeParameterType(),
        );
        $sort = $this->urlParameterProvider->get(
            $this->urlConfig->getSortParameterName(),
            $this->urlConfig->getSortParameterType(),
        );

        try {
            $preparedDataReader = $this->prepareDataReaderByParams($page, $previousPage, $pageSize, $sort, $filters);
            return [$preparedDataReader, $this->getItems($preparedDataReader)];
        } catch (InvalidPageException $exception) {
        }

        if ($this->ignoreMissingPage) {
            $preparedDataReader = $this->prepareDataReaderByParams(null, null, $pageSize, $sort, $filters);
            try {
                return [$preparedDataReader, $this->getItems($preparedDataReader)];
            } catch (InvalidPageException $exception) {
            }
        }

        if ($this->pageNotFoundExceptionCallback !== null) {
            ($this->pageNotFoundExceptionCallback)($exception);
        }

        throw $exception;
    }

    /**
     * @param FilterInterface[] $filters
     *
     * @throws InvalidPageException
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

        $dataReader = $dataReader->withPageSize(
            $this->preparePageSize($pageSize) ?? $this->getDefaultPageSize(),
        );

        if ($dataReader->isPaginationRequired()) {
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
                if (!$this->multiSort) {
                    $order = array_slice($order, 0, 1, true);
                }
                $dataReader = $dataReader->withSort(
                    $sortObject->withOrder(
                        $this->prepareOrder($order)
                    )
                );
            }
        }

        if (!empty($filters) && $dataReader->isFilterable()) {
            $dataReader = $dataReader->withFilter(
                new AndX($dataReader->getFilter(), ...$filters),
            );
        }

        return $dataReader;
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
     * @throws PageNotFoundException
     *
     * @psalm-return array<array-key, array|object>
     */
    private function getItems(ReadableDataInterface $dataReader): array
    {
        $items = $dataReader->read();
        return is_array($items) ? $items : iterator_to_array($items);
    }

    private function renderHeader(): string
    {
        if ($this->header === '') {
            return '';
        }

        return $this->headerTag === null
            ? $this->header
            : Html::tag($this->headerTag, $this->header, $this->headerAttributes)
                ->encode($this->encodeHeader)
                ->render();
    }

    private function renderSummary(ReadableDataInterface|null $dataReader): string|Stringable
    {
        if (empty($this->summaryTemplate) || !$dataReader instanceof OffsetPaginator) {
            return '';
        }

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
            : Html::tag($this->summaryTag, $content, $this->summaryAttributes)->encode(false);
    }

    private function renderPagination(ReadableDataInterface|null $dataReader): string|Stringable
    {
        if (!$dataReader instanceof PaginatorInterface) {
            return '';
        }

        if ($this->paginationWidget === null) {
            if ($dataReader instanceof OffsetPaginator) {
                $widget = OffsetPagination::widget(config: $this->offsetPaginationConfig)->paginator($dataReader);
            } elseif ($dataReader instanceof KeysetPaginator) {
                $widget = KeysetPagination::widget(config: $this->keysetPaginationConfig)->paginator($dataReader);
            } else {
                return '';
            }
        } else {
            $widget = $this->paginationWidget->paginator($dataReader);
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
            $nextUrlPattern,
            $previousUrlPattern,
            $defaultUrl,
        );

        return $widget->context($context)->render();
    }

    private function renderPageSize(ReadableDataInterface|null $dataReader): string
    {
        if (empty($this->pageSizeTemplate) || !$dataReader instanceof PaginatorInterface) {
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

        return OrderHelper::arrayToString($sort->getOrder());
    }

    private function getSort(ReadableDataInterface|null $dataReader): ?Sort
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
}
