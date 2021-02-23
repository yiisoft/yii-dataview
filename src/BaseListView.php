<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use JsonException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\Exception\InvalidConfigException;
use Yiisoft\Yii\DataView\Widget\LinkPager;
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
    public const BOOTSTRAP = 'bootstrap';
    public const BULMA = 'bulma';
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
    private const FRAMEWORKCSS = [
        self::BOOTSTRAP,
        self::BULMA,
    ];
    private string $frameworkCss = self::BOOTSTRAP;
    private int $pageSize = 0;
    private int $currentPage = 1;
    private array $requestAttributes = [];
    private array $requestQueryParams = [];
    private TranslatorInterface $translator;

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

    protected function run(): string
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
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        return Html::tag($tag, $content)->attributes($options)->encode(false)->render();
    }

    public function getPaginator(): PaginatorInterface
    {
        return $this->paginator;
    }

    /**
     * @param int $currentPage set current page PaginatorInterface::class {@see OffsetPaginator::currentPage()}
     * {@see KeysetPaginator::
     *
     * @return $this
     */
    public function currentPage(int $currentPage): self
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
    public function emptyText(?string $emptyText): self
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
    public function emptyTextOptions(array $emptyTextOptions): self
    {
        $new = clone $this;
        $new->emptyTextOptions = $emptyTextOptions;

        return $new;
    }

    public function frameworkCss(string $frameworkCss): self
    {
        if (!in_array($frameworkCss, self::FRAMEWORKCSS)) {
            $frameworkCss = implode('", "', self::FRAMEWORKCSS);
            throw new InvalidConfigException("Invalid framework css. Valid values are: \"$frameworkCss\".");
        }

        $new = clone $this;
        $new->frameworkCss = $frameworkCss;

        return $new;
    }

    public function getRequestAttributes(): array
    {
        return $this->requestAttributes;
    }

    public function getRequestQueryParams(): array
    {
        return $this->requestQueryParams;
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
    public function layout(string $layout): self
    {
        $new = clone $this;
        $new->layout = $layout;

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
    public function options(array $options): self
    {
        $new = clone $this;
        $new->options = ArrayHelper::merge($this->options, $options);

        return $new;
    }

    /**
     * @param int $pageSize set page size PaginatorInterface {@see OffsetPaginator::pageSize()}
     * {@see KeysetPaginator::pageSize()}.
     *
     * @return $this
     */
    public function pageSize(int $pageSize): self
    {
        $new = clone $this;
        $new->pageSize = $pageSize;

        return $new;
    }

    /**
     * @param PaginatorInterface $paginator set paginator {@see OffsetPaginator} {@see KeysetPaginator}.
     *
     * @return $this
     */
    public function paginator(PaginatorInterface $paginator): self
    {
        $new = clone $this;
        $new->paginator = $paginator;

        return $new;
    }

    public function requestAttributes(array $requestAttributes): self
    {
        $new = clone $this;
        $new->requestAttributes = $requestAttributes;

        return $new;
    }

    public function requestQueryParams(array $requestQueryParams): self
    {
        $new = clone $this;
        $new->requestQueryParams = $requestQueryParams;

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
    public function showOnEmpty(bool $showOnEmpty): self
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
    public function summary(string $summary): self
    {
        $new = clone $this;
        $new->summary = $summary;

        return $new;
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
    public function summaryOptions(array $summaryOptions): self
    {
        $new = clone $this;
        $new->summaryOptions = $summaryOptions;

        return $new;
    }

    public function showEmptyText(bool $value): self
    {
        $new = clone $this;
        $new->showEmptyText = $value;

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

    /**
     * Renders the HTML content indicating that the list view has no data.
     *
     * @throws JsonException
     *
     * @return string the rendering result
     *
     * @see emptyText
     */
    protected function renderEmpty(): string
    {
        if (!$this->showEmptyText) {
            return '';
        }

        $options = $this->emptyTextOptions;
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        return Html::tag($tag, $this->emptyText)->attributes($options)->render();
    }

    /**
     * Renders the summary text.
     */
    private function renderSummary(): string
    {
        $count = $this->paginator->getTotalItems();

        if ($count < 1 || $this->summary === '') {
            return '';
        }

        $summaryOptions = $this->summaryOptions;
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
                'yii-gridview',
            ),
        )
        ->attributes($summaryOptions)
        ->encode(false)
        ->render();
    }

    /**
     * Renders the pager.
     *
     * @throws InvalidConfigException|\Yiisoft\Factory\Exceptions\InvalidConfigException
     *
     * @return string the rendering result
     */
    private function renderPager(): string
    {
        if ($this->paginator === null || $this->paginator->getTotalItems() < 1) {
            return '';
        }

        return LinkPager::widget()
            ->frameworkCss($this->frameworkCss)
            ->paginator($this->paginator)
            ->requestAttributes($this->requestAttributes)
            ->requestQueryParams($this->requestQueryParams)
            ->render();
    }

    /**
     * Renders a section of the specified name.
     *
     * If the named section is not supported, false will be returned.
     *
     * @param string $name the section name, e.g., `{summary}`, `{items}`.
     *
     * @throws InvalidConfigException|\Yiisoft\Factory\Exceptions\InvalidConfigException
     *
     * @return bool|string the rendering result of the section, or false if the named section is not supported.
     */
    private function renderSection(string $name): string
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
     * Renders the sorter.
     *
     * @throws \Yiisoft\Factory\Exceptions\InvalidConfigException
     *
     * @return string the rendering result
     */
    private function renderSorter(): string
    {
        $sort = $this->paginator->getSort();

        if ($sort === null || empty($sort->getCriteria()) || $this->paginator->getTotalItems() < 1) {
            return '';
        }

        return LinkSorter::widget()->sort($sort)->frameworkCss($this->frameworkCss)->render();
    }
}
