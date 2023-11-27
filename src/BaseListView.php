<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Html\Tag\Div;
use Yiisoft\Html\Tag\Td;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IdMessageReader;
use Yiisoft\Translator\IntlMessageFormatter;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\DataView\Exception\DataReaderNotSetException;

abstract class BaseListView extends Widget
{
    /**
     * A name for {@see CategorySource} used with translator ({@see TranslatorInterface}) by default.
     */
    final public const DEFAULT_TRANSLATION_CATEGORY = 'yii-dataview';

    /**
     * @var TranslatorInterface A translator instance used for translations of messages. If it was not set
     * explicitly in the constructor, a default one created automatically in {@see createDefaultTranslator()}.
     */
    protected readonly TranslatorInterface $translator;

    private array $attributes = [];
    protected ?string $emptyText = null;
    private array $emptyTextAttributes = [];
    private string $header = '';
    private array $headerAttributes = [];
    private string $layout = "{header}\n{toolbar}";
    private string $layoutGridTable = "{items}\n{summary}\n{pager}";
    private string $pagination = '';
    protected ?ReadableDataInterface $dataReader = null;
    protected array $sortLinkAttributes = [];
    private ?string $summary = null;
    private array $summaryAttributes = [];
    private string $toolbar = '';
    protected array $urlArguments = [];
    protected array $urlQueryParameters = [];
    private bool $withContainer = true;

    public function __construct(
        TranslatorInterface|null $translator = null,
        private UrlGeneratorInterface|null $urlGenerator = null,
        protected readonly string $translationCategory = self::DEFAULT_TRANSLATION_CATEGORY,
    ) {
        $this->translator = $translator ?? $this->createDefaultTranslator();
    }

    /**
     * Renders the data models.
     *
     * @return string the rendering result.
     */
    abstract protected function renderItems(): string;

    /**
     * Returns a new instance with the HTML attributes. The following special options are recognized.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function attributes(array $values): static
    {
        $new = clone $this;
        $new->attributes = $values;

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

    public function getUrlGenerator(): UrlGeneratorInterface
    {
        if ($this->urlGenerator === null) {
            throw new Exception\UrlGeneratorNotSetException();
        }

        return $this->urlGenerator;
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
     * @param string $value The id of the grid view, detail view, or list view.
     */
    public function id(string $value): static
    {
        $new = clone $this;
        $new->attributes['id'] = $value;

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

    /**
     * Returns a new instance with the layout grid table.
     *
     * @param string $value The layout that determines how different sections of the grid view, list view. Should be
     * organized.
     *
     * The following tokens will be replaced with the corresponding section contents:
     *
     * - `{items}`: The items section.
     * - `{summary}`: The summary section.
     * - `{pager}`: The pager section.
     */
    public function layoutGridTable(string $value): static
    {
        $new = clone $this;
        $new->layoutGridTable = $value;

        return $new;
    }

    /**
     * Returns a new instance with the pagination of the grid view, detail view, or list view.
     *
     * @param string $value The pagination of the grid view, detail view, or list view.
     */
    public function pagination(string $value): static
    {
        $new = clone $this;
        $new->pagination = $value;

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

    /**
     * Return new instance with the HTML attributes for widget link sort.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function sortLinkAttributes(array $values): static
    {
        $new = clone $this;
        $new->sortLinkAttributes = $values;

        return $new;
    }

    /**
     * Returns a new instance with the summary of the grid view, detail view, and list view.
     *
     * @param string $value the HTML content to be displayed as the summary of the list view.
     *
     * If you do not want to show the summary, you may set it with an empty string.
     *
     * The following tokens will be replaced with the corresponding values:
     *
     * - `{begin}`: the starting row number (1-based) currently being displayed.
     * - `{end}`: the ending row number (1-based) currently being displayed.
     * - `{count}`: the number of rows currently being displayed.
     * - `{totalCount}`: the total number of rows available.
     * - `{page}`: the page number (1-based) current being displayed.
     * - `{pageCount}`: the number of pages available.
     */
    public function summary(?string $value): static
    {
        $new = clone $this;
        $new->summary = $value;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for summary of grid view, detail view, and list view.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function summaryAttributes(array $values): static
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
     * @psalm-param array<array-key,array> $toolbar
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

    /**
     * Returns a new instance whether container is enabled or not.
     *
     * @param bool $value Whether container is enabled or not.
     */
    public function withContainer(bool $value = true): static
    {
        $new = clone $this;
        $new->withContainer = $value;

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

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    protected function renderLinkSorter(string $attribute, string $label): string
    {
        $dataReader = $this->getDataReader();
        if (!$dataReader instanceof PaginatorInterface) {
            return '';
        }

        $sort = $dataReader->getSort();
        if ($sort === null) {
            return '';
        }

        if ($dataReader instanceof OffsetPaginator) {
            $linkSorter = LinkSorter::widget()->currentPage($dataReader->getCurrentPage());
        } elseif ($dataReader instanceof KeysetPaginator) {
            $linkSorter = LinkSorter::widget();
        } else {
            return '';
        }

        return $linkSorter
            ->attribute($attribute)
            ->attributes($sort->getCriteria())
            ->directions($sort->getOrder())
            ->iconAscClass('bi bi-sort-alpha-up')
            ->iconDescClass('bi bi-sort-alpha-down')
            ->label($label)
            ->linkAttributes($this->sortLinkAttributes)
            ->pageSize($dataReader->getPageSize())
            ->urlArguments($this->urlArguments)
            ->urlQueryParameters($this->urlQueryParameters)
            ->render();
    }

    public function render(): string
    {
        if ($this->dataReader === null) {
            throw new DataReaderNotSetException();
        }

        return $this->renderGrid();
    }

    /**
     * @psalm-return array<array-key, array|object>
     */
    protected function getItems(): array
    {
        $data = $this->getDataReader()->read();
        return is_array($data) ? $data : iterator_to_array($data);
    }

    private function renderPagination(): string
    {
        $dataReader = $this->getDataReader();
        if (!$dataReader instanceof PaginatorInterface) {
            return '';
        }

        return $dataReader->isRequired() ? $this->pagination : '';
    }

    private function renderSummary(): string
    {
        $dataReader = $this->getDataReader();
        if (!$dataReader instanceof OffsetPaginator) {
            return '';
        }

        $data = iterator_to_array($dataReader->read());
        $pageCount = count($data);

        if ($pageCount <= 0) {
            return '';
        }

        $summary = $this->translator->translate(
            $this->summary ?? 'Page <b>{currentPage}</b> of <b>{totalPages}</b>',
            [
                'currentPage' => $dataReader->getCurrentPage(),
                'totalPages' => $dataReader->getTotalPages(),
            ],
            $this->translationCategory,
        );

        return Div::tag()->attributes($this->summaryAttributes)->content($summary)->encode(false)->render();
    }

    private function renderGrid(): string
    {
        $attributes = $this->attributes;
        $contentGrid = '';

        if ($this->layout !== '') {
            $contentGrid = trim(
                strtr($this->layout, ['{header}' => $this->renderHeader(), '{toolbar}' => $this->toolbar])
            );
        }

        return match ($this->withContainer) {
            true => trim(
                $contentGrid . "\n" . Div::tag()
                    ->attributes($attributes)
                    ->content("\n" . $this->renderGridTable() . "\n")
                    ->encode(false)
                    ->render()
            ),
            false => trim($contentGrid . "\n" . $this->renderGridTable()),
        };
    }

    private function renderGridTable(): string
    {
        return trim(
            strtr(
                $this->layoutGridTable,
                [
                    '{header}' => $this->renderHeader(),
                    '{toolbar}' => $this->toolbar,
                    '{items}' => $this->renderItems(),
                    '{summary}' => $this->renderSummary(),
                    '{pager}' => $this->renderPagination(),
                ],
            )
        );
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
