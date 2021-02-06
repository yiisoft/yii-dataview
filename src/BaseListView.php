<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use JsonException;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\Widget\Bulma\LinkPager;
use Yiisoft\Yii\DataView\Widget\LinkSorter;

use function count;
use function preg_replace_callback;

/**
 * BaseListView is a base class for widgets displaying data from data provider such as ListView and GridView.
 *
 * It provides features like sorting, paging and also filtering the data.
 *
 * For more details and usage information on BaseListView:
 * see the [guide article on data widgets](guide:output-data-widgets).
 */
abstract class BaseListView extends Widget
{
    protected array $options = [];
    protected PaginatorInterface $paginator;
    protected ?LinkSorter $sorter = null;
    protected string $summary = 'Showing <b>{begin, number}-{end, number}</b> of <b>{totalCount, number}</b> ' .
        '{totalCount, plural, one{item} other{items}}';
    protected array $summaryOptions = [];
    protected bool $showOnEmpty = false;
    protected ?string $emptyText = 'No results found.';
    protected bool $showEmptyText = true;
    protected array $emptyTextOptions = ['class' => 'empty'];
    protected string $layout = "{summary}\n{items}\n{pager}";
    private string $linkPagerClass = LinkPager::class;
    private int $pageSize = 0;
    private int $currentPage = 1;
    private TranslatorInterface $translator;
    private ?ServerRequestInterface $serverRequest = null;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Renders the data models.
     *
     * @return string the rendering result.
     */
    abstract public function renderItems(): string;

    public function run(): string
    {
        if ($this->showOnEmpty || ($this->paginator->getTotalItems() > 0)) {
            $content = preg_replace_callback(
                '/{\\w+}/',
                function ($matches) {
                    $content = $this->renderSection($matches[0]);

                    return $content === false ? $matches[0] : $content;
                },
                $this->layout,
            );
        } else {
            $content = $this->renderEmpty();
        }

        $options = $this->options;
        $options['encode'] = false;
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        return Html::tag($tag, $content, $options);
    }

    /**
     * Renders a section of the specified name.
     *
     * If the named section is not supported, false will be returned.
     *
     * @param string $name the section name, e.g., `{summary}`, `{items}`.
     *
     * @return bool|string the rendering result of the section, or false if the named section is not supported.
     */
    public function renderSection(string $name): string
    {
        switch ($name) {
            case '{summary}':
                return $this->renderSummary();
            case '{items}':
                return $this->renderItems();
            case '{pager}':
                return $this->renderPager();
            case '{sorter}':
                return $this->renderSorter();
            default:
                return '';
        }
    }

    /**
     * Renders the HTML content indicating that the list view has no data.
     *
     * @throws JsonException
     *
     * @return string the rendering result
     *
     * @see emptyText
     */
    public function renderEmpty(): string
    {
        if (!$this->showEmptyText) {
            return '';
        }

        $options = $this->emptyTextOptions;
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        return Html::tag($tag, $this->emptyText, $options);
    }

    /**
     * Renders the summary text.
     */
    public function renderSummary(): string
    {
        $count = $this->paginator->getTotalItems();

        if ($count < 1 || $this->summary === '') {
            return '';
        }

        $summaryOptions = $this->summaryOptions;
        $summaryOptions['encode'] = false;
        $tag = ArrayHelper::remove($summaryOptions, 'tag', 'div');

        if ($this->paginator instanceof OffsetPaginator) {
            $totalCount = count($this->getDataReader());
            $begin = $this->paginator->getOffset() + 1;
            $end = ($begin + $totalCount) - 1;

            if ($begin > $end) {
                $begin = $end;
            }

            $page = $this->paginator->getCurrentPage() + 1;
            $pageCount = $this->paginator->getCurrentPageSize();
        } else {
            $begin = $page = $pageCount = 1;
            $end = $totalCount = $count;
        }

        return Html::tag(
            $tag,
            $this->translator->translate(
                $this->summary,
                [
                    'begin' => $begin,
                    'end' => $end,
                    'count' => $count,
                    'totalCount' => $this->summary === null ? $totalCount : $count,
                    'page' => $page,
                    'pageCount' => $pageCount,
                ],
                'user',
            ),
            $summaryOptions
        );
    }

    /**
     * Renders the pager.
     *
     * @return string the rendering result
     */
    public function renderPager(): string
    {
        if ($this->paginator === null || $this->paginator->getTotalItems() < 1) {
            return '';
        }

        /** @var $class LinkPager */
        $pager = $this->linkPagerClass::widget();

        return $pager
            ->withPaginator($this->paginator)
            ->withServerRequest($this->serverRequest)
            ->render();
    }

    /**
     * Renders the sorter.
     *
     * @return string the rendering result
     */
    public function renderSorter(): string
    {
        $sort = $this->paginator->getSort();

        if (
            $this->sorter === null ||
            $sort === null ||
            empty($sort->getCriteria()) ||
            $this->paginator->getTotalItems() < 1
        ) {
            return '';
        }

        $this->sorter->sort($sort);

        return $this->sorter->run();
    }

    public function getPaginator(): PaginatorInterface
    {
        return $this->paginator;
    }

    /**
     * @param int $currentPage set current page PaginatorInterface::class {@see OffsetPaginator::withCurrentPage()}
     * {@see KeysetPaginator::
     *
     * @return $this
     */
    public function withCurrentPage(int $currentPage): self
    {
        $new = clone $this;
        $new->currentPage = $currentPage;

        return $new;
    }

    /**
     * @param string|null $emptyText the HTML content to be displayed when {@see} does not have any data.
     *
     * When this is set to `false` no extra HTML content will be generated.
     *
     * The default value is the text "No results found." which will be translated to the current
     * application language.
     *
     * @return $this
     *
     * @see showOnEmpty
     * @see emptyTextOptions
     */
    public function withEmptyText(?string $emptyText): self
    {
        $new = clone $this;

        if ($emptyText !== null) {
            $new->emptyText = $emptyText;
        }

        return $new;
    }

    /**
     * @param array $emptyTextOptions the HTML attributes for the emptyText of the list view.
     *
     * The "tag" element specifies the tag name of the emptyText element and defaults to "div".
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function withEmptyTextOptions(array $emptyTextOptions): self
    {
        $new = clone $this;
        $new->emptyTextOptions = $emptyTextOptions;

        return $new;
    }

    /**
     * @param string $layout the layout that determines how different sections of the list view should be organized.
     *
     * The following tokens will be replaced with the corresponding section contents:
     * - `{summary}`: the summary section. See {@see renderSummary()}.
     * - `{items}`: the list items. See {@see renderItems()}.
     * - `{sorter}`: the sorter. See {@see renderSorter()}.
     * - `{pager}`: the pager. See {@see renderPager()}.
     *
     * @return $this
     */
    public function withLayout(string $layout): self
    {
        $new = clone $this;
        $new->layout = $layout;

        return $new;
    }

    /**
     * @param string $linkPagerClass class for widget {@see LinkPager}.
     *
     * @return $this
     */
    public function withLinkPagerClass(string $linkPagerClass): self
    {
        $new = clone $this;
        $new->linkPagerClass = $linkPagerClass;

        return $new;
    }

    /**
     * @param array $options the HTML attributes for the container tag of the list view.
     *
     * The "tag" element specifies the tag name of the container element and defaults to "div".
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function withOptions(array $options): self
    {
        $new = clone $this;

        $new->options = ArrayHelper::merge($this->options, $options);

        return $new;
    }

    /**
     * @param int $pageSize set page size PaginatorInterface {@see OffsetPaginator::withPageSize()}
     * {@see KeysetPaginator::withPageSize()}.
     *
     * @return $this
     */
    public function withPageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * @param PaginatorInterface $paginator set paginator {@see OffsetPaginator} {@see KeysetPaginator}.
     *
     * @return $this
     */
    public function withPaginator(PaginatorInterface $paginator): self
    {
        $new = clone $this;
        $new->paginator = $paginator;

        return $new;
    }

    public function withServerRequest(ServerRequestInterface $serverRequest): self
    {
        $new = clone $this;
        $new->serverRequest = $serverRequest;

        return $new;
    }

    /**
     * @param bool $showOnEmpty whether to show an empty list view if {@see} returns no data.
     *
     * The default value is false which displays an element according to the {@see $emptyText} and
     * {@see $emptyTextOptions} properties.
     *
     * @return $this
     */
    public function withShowOnEmpty(bool $showOnEmpty): self
    {
        $new = clone $this;
        $new->showOnEmpty = $showOnEmpty;

        return $new;
    }

    /**
     * @param string $summary the HTML content to be displayed as the summary of the list view.
     *
     * If you do not want to show the summary, you may set it with an empty string.
     *
     * The following tokens will be replaced with the corresponding values:
     * - `{begin}`: the starting row number (1-based) currently being displayed
     * - `{end}`: the ending row number (1-based) currently being displayed
     * - `{count}`: the number of rows currently being displayed
     * - `{totalCount}`: the total number of rows available
     * - `{page}`: the page number (1-based) current being displayed
     * - `{pageCount}`: the number of pages available
     *
     * @return $this
     */
    public function withSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @param array $summaryOptions the HTML attributes for the summary of the list view.
     *
     * The "tag" element specifies the tag name of the summary element and defaults to "div".
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function withSummaryOptions(array $summaryOptions): self
    {
        $new = clone $this;
        $new->summaryOptions = $summaryOptions;

        return $new;
    }

    public function withShowEmptyText(bool $value): self
    {
        $new = clone $this;
        $new->showEmptyText = $value;

        return $new;
    }

    public function withSorter(?LinkSorter $sorter): self
    {
        $new = clone $this;
        $new->sorter = $sorter;

        return $new;
    }

    protected function getDataReader(): array
    {
        $dataReader = [];

        if ($this->pageSize > 0) {
            $this->paginator = $this->paginator->withPageSize($this->pageSize);
        }

        $this->paginator = $this->paginator->withCurrentPage($this->currentPage);

        foreach ($this->paginator->read() as $read) {
            $dataReader[] = $read;
        }

        return $dataReader;
    }
}
