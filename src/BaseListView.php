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
use Yiisoft\Data\Reader\FilterableDataInterface;
use Yiisoft\Data\Reader\LimitableDataInterface;
use Yiisoft\Data\Reader\OffsetableDataInterface;
use Yiisoft\Data\Reader\ReadableDataInterface;
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
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\DataView\Exception\DataReaderNotSetException;

/**
 * @psalm-type UrlArguments = array<string,scalar|Stringable|null>
 * @psalm-type UrlCreator = callable(UrlArguments,array):string
 * @psalm-type PageNotFoundExceptionCallback = callable(PageNotFoundException):void
 */
abstract class BaseListView extends Widget
{
    /**
     * @psalm-var UrlCreator|null
     */
    protected $urlCreator = null;
    protected UrlConfig $urlConfig;

    protected int $defaultPageSize = PaginatorInterface::DEFAULT_PAGE_SIZE;

    /**
     * A name for {@see CategorySource} used with translator ({@see TranslatorInterface}) by default.
     */
    final public const DEFAULT_TRANSLATION_CATEGORY = 'yii-dataview';

    /**
     * @var TranslatorInterface A translator instance used for translations of messages. If it was not set
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
    private string $layout = "{header}\n{toolbar}\n{items}\n{summary}\n{pager}";
    private string|OffsetPagination|KeysetPagination|null $pagination = null;
    protected ?ReadableDataInterface $dataReader = null;
    private string $toolbar = '';

    /**
     * @psalm-var array<string,scalar|Stringable|null>
     */
    protected array $urlArguments = [];
    protected array $urlQueryParameters = [];

    private UrlParameterProviderInterface|null $urlParameterProvider = null;

    private bool $resetPageOnPageNotFound = false;

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

    final public function resetPageOnPageNotFound(bool $reset = true): static
    {
        $new = clone $this;
        $new->resetPageOnPageNotFound = $reset;
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
     * Return a new instance with name of argument or query parameter for page.
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
     * Return a new instance with name of argument or query parameter for page size.
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

    /**
     * Renders the data models.
     *
     * @return string the rendering result.
     *
     * @psalm-param array<array-key, array|object> $items
     */
    abstract protected function renderItems(array $items): string;

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
     * @param string $emptyText the HTML content to be displayed when {@see dataProvider} does not have any data.
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
     * @param array $values Attribute values indexed by attribute names.
     */
    public function emptyTextAttributes(array $values): static
    {
        $new = clone $this;
        $new->emptyTextAttributes = $values;

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
     * @throws PageNotFoundException
     *
     * @psalm-return array<array-key, array|object>
     */
    private function prepareDataReaderAndGetItems(): array
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

        $this->preparedDataReader = $this->prepareDataReaderByParams($page, $previousPage, $pageSize, $sort);

        try {
            return $this->getItems($this->preparedDataReader);
        } catch (PageNotFoundException $exception) {
        }

        if ($this->resetPageOnPageNotFound) {
            $this->preparedDataReader = $this->prepareDataReaderByParams(null, null, $pageSize, $sort);
            try {
                return $this->getItems($this->preparedDataReader);
            } catch (PageNotFoundException) {
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

    private function prepareDataReaderByParams(
        ?string $page,
        ?string $previousPage,
        ?string $pageSize,
        ?string $sort,
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
            if ($pageSize !== null) {
                $dataReader = $dataReader->withPageSize((int) $pageSize);
            }

            if ($page !== null) {
                $dataReader = $dataReader->withToken(PageToken::next($page));
            } elseif ($previousPage !== null) {
                $dataReader = $dataReader->withToken(PageToken::previous($previousPage));
            }
        }

        if ($dataReader->isSortable() && !empty($sort)) {
            $sortObject = $dataReader->getSort();
            if ($sortObject !== null) {
                $dataReader = $dataReader->withSort($sortObject->withOrderString($sort));
            }
        }

        return $dataReader;
    }

    /**
     * Return new instance with the header for the grid.
     *
     * @param string $value The header of the grid.
     *
     * {@see headerAttributes}
     */
    public function header(string $value): self
    {
        $new = clone $this;
        $new->header = $value;

        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the header.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function headerAttributes(array $values): self
    {
        $new = clone $this;
        $new->headerAttributes = $values;

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
     * @param string $value The template that determines how different sections of the grid view, list view. Should be
     * organized.
     *
     * The following tokens will be replaced with the corresponding section contents:
     *
     * - `{header}`: The header section.
     * - `{toolbar}`: The toolbar section.
     */
    public function layout(string $value): static
    {
        $new = clone $this;
        $new->layout = $value;

        return $new;
    }

    public function pagination(string|KeysetPagination|OffsetPagination|null $pagination): static
    {
        $new = clone $this;
        $new->pagination = $pagination;
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
     * @param string|null $template The HTML content to be displayed as the summary. If you do not want to show
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
     * @param array $values Attribute values indexed by attribute names.
     */
    final public function summaryAttributes(array $values): static
    {
        $new = clone $this;
        $new->summaryAttributes = $values;
        return $new;
    }

    /**
     * Return new instance with toolbar content.
     *
     * @param string $value The toolbar content.
     *
     * @psalm-param array $toolbar
     */
    public function toolbar(string $value): self
    {
        $new = clone $this;
        $new->toolbar = $value;

        return $new;
    }

    /**
     * Return a new instance with arguments of the route.
     *
     * @param array $value Arguments of the route.
     *
     * @psalm-param array<string,scalar|Stringable|null> $value
     */
    public function urlArguments(array $value): static
    {
        $new = clone $this;
        $new->urlArguments = $value;
        return $new;
    }

    /**
     * Return a new instance with query parameters of the route.
     *
     * @param array $value The query parameters of the route.
     */
    public function urlQueryParameters(array $value): static
    {
        $new = clone $this;
        $new->urlQueryParameters = $value;

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
        $items = $this->prepareDataReaderAndGetItems();

        $content = trim(
            strtr(
                $this->layout,
                [
                    '{header}' => $this->renderHeader(),
                    '{toolbar}' => $this->toolbar,
                    '{items}' => $this->renderItems($items),
                    '{summary}' => $this->renderSummary(),
                    '{pager}' => $this->renderPagination(),
                ],
            )
        );

        return $this->containerTag === null
            ? $content
            : Html::tag($this->containerTag, "\n" . $content . "\n", $this->containerAttributes)
                ->encode(false)
                ->render();
    }

    protected function getDefaultPageSize(): int
    {
        $dataReader = $this->getDataReader();
        if ($dataReader instanceof PaginatorInterface) {
            return $dataReader->getPageSize();
        }

        return $this->defaultPageSize;
    }

    private function renderPagination(): string
    {
        $preparedDataReader = $this->preparedDataReader;
        if (!$preparedDataReader instanceof PaginatorInterface || !$preparedDataReader->isPaginationRequired()) {
            return '';
        }

        if (is_string($this->pagination)) {
            return $this->pagination;
        }

        if ($this->pagination === null) {
            if ($preparedDataReader instanceof OffsetPaginator) {
                $pagination = OffsetPagination::widget();
            } elseif ($preparedDataReader instanceof KeysetPaginator) {
                $pagination = KeysetPagination::widget();
            } else {
                return '';
            }
        } else {
            $pagination = $this->pagination;
        }

        if ($pagination instanceof OffsetPagination && $preparedDataReader instanceof OffsetPaginator) {
            $pagination = $pagination->paginator($preparedDataReader);
        } elseif ($pagination instanceof KeysetPagination && $preparedDataReader instanceof KeysetPaginator) {
            $pagination = $pagination->paginator($preparedDataReader);
        } else {
            return '';
        }

        if ($this->urlCreator !== null) {
            $pagination = $pagination->urlCreator($this->urlCreator);
        }

        return $pagination
            ->defaultPageSize($this->getDefaultPageSize())
            ->urlConfig($this->urlConfig)
            ->render();
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
     * Creates default translator to use if {@see $translator} was not set explicitly in the constructor. Depending on
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
