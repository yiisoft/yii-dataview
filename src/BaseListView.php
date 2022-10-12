<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Html\Tag\Div;
use Yiisoft\Html\Tag\Td;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\DataView\Widget\LinkSorter;

abstract class BaseListView extends Widget
{
    private array $attributes = [];
    protected string $emptyText = 'No results found.';
    private array $emptyTextAttributes = [];
    private string $header = '';
    private array $headerAttributes = [];
    private string $layout = "{header}\n{toolbar}";
    private string $layoutGridTable = "{items}\n{summary}\n{pager}";
    private string $pagination = '';
    protected ?PaginatorInterface $paginator = null;
    private array $sortLinkAttributes = [];
    private string $summary = 'gridview.summary';
    private array $summaryAttributes = [];
    private TranslatorInterface|null $translator = null;
    private string $toolbar = '';
    protected array $urlArguments = [];
    protected UrlGeneratorInterface|null $urlGenerator = null;
    protected string $urlName = '';
    protected array $urlQueryParameters = [];
    private bool $withContainer = true;

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
    public function emptyText(string $emptyText): static
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

    public function getPaginator(): PaginatorInterface
    {
        if ($this->paginator === null) {
            throw new Exception\PaginatorNotSetException();
        }

        return $this->paginator;
    }

    public function getTranslator(): TranslatorInterface
    {
        if ($this->translator === null) {
            throw new Exception\TranslatorNotSetException();
        }

        return $this->translator;
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
     * @param PaginatorInterface $value The paginator interface of the grid view, detail view, or list view.
     */
    public function paginator(PaginatorInterface $value): static
    {
        $new = clone $this;
        $new->paginator = $value;

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
    public function summary(string $value): static
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
     * Returns a new instance with the translator interface of the grid view, detail view, or list view.
     *
     * @param TranslatorInterface $value The translator interface of the grid view, detail view, or list view.
     */
    public function translator(TranslatorInterface $value): static
    {
        $new = clone $this;
        $new->translator = $value;

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
     * Return a new instance with URL generator interface for pagination.
     *
     * @param UrlGeneratorInterface $value The URL generator interface for pagination.
     */
    public function urlGenerator(UrlGeneratorInterface $value): static
    {
        $new = clone $this;
        $new->urlGenerator = $value;

        return $new;
    }

    /**
     * Returns a new instance with the name of the route.
     *
     * @param string $value The name of the route.
     */
    public function urlName(string $value): static
    {
        $new = clone $this;
        $new->urlName = $value;

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

    protected function getDataReader(): array
    {
        $dataReader = [];

        /** @var array */
        foreach ($this->getPaginator()->read() as $read) {
            $dataReader[] = $read;
        }

        return $dataReader;
    }

    protected function renderEmpty(int $colspan): Td
    {
        $emptyTextAttributes = $this->emptyTextAttributes;
        $emptyText = $this->getTranslator()->translate($this->emptyText, [], 'gridview');
        $emptyTextAttributes['colspan'] = $colspan;

        return Td::tag()->addAttributes($emptyTextAttributes)->content($emptyText);
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    protected function renderLinkSorter(string $attribute, string $label): string
    {
        $renderLinkSorter = '';
        $paginator = $this->getPaginator();
        $sort = $paginator->getSort();
        $linkSorter = LinkSorter::widget();

        if ($paginator instanceof OffsetPaginator) {
            $linkSorter = $linkSorter->currentPage($paginator->getCurrentPage());
        }

        if ($sort !== null) {
            $renderLinkSorter = $linkSorter
                ->attribute($attribute)
                ->attributes($sort->getCriteria())
                ->directions($sort->getOrder())
                ->iconAscClass('bi bi-sort-alpha-up')
                ->iconDescClass('bi bi-sort-alpha-down')
                ->label($label)
                ->linkAttributes($this->sortLinkAttributes)
                ->pageSize($this->getPaginator()->getPageSize())
                ->urlArguments($this->urlArguments)
                ->urlGenerator($this->getUrlGenerator())
                ->urlName($this->urlName)
                ->urlQueryParameters($this->urlQueryParameters)
                ->render();
        }

        return $renderLinkSorter;
    }

    protected function run(): string
    {
        if ($this->paginator === null) {
            throw new Exception\PaginatorNotSetException();
        }

        return $this->renderGrid();
    }

    private function renderPagination(): string
    {
        return match ($this->getPaginator()->isRequired()) {
            true => $this->pagination,
            false => '',
        };
    }

    private function renderSummary(): string
    {
        $pageCount = count($this->getDataReader());

        if ($this->getPaginator() instanceof KeysetPaginator) {
            return '';
        }

        if ($pageCount <= 0) {
            return '';
        }

        /** @var OffsetPaginator $paginator */
        $paginator = $this->getPaginator();

        return Div::tag()
            ->addAttributes($this->summaryAttributes)
            ->content(
                $this->getTranslator()->translate(
                    $this->summary,
                    [
                        'currentPage' => $paginator->getCurrentPage(),
                        'totalPages' => $paginator->getTotalPages(),
                    ],
                    'gridview',
                )
            )
            ->encode(false)
            ->render();
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
                $contentGrid . PHP_EOL . Div::tag()
                    ->addAttributes($attributes)
                    ->content(PHP_EOL . $this->renderGridTable() . PHP_EOL)
                    ->encode(false)
                    ->render()
            ),
            false => trim($contentGrid . PHP_EOL . $this->renderGridTable()),
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
                ->addAttributes($this->headerAttributes)
                ->content($this->header)
                ->encode(false)
                ->render(),
        };
    }
}
