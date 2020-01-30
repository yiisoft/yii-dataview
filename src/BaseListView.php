<?php

namespace Yiisoft\Yii\DataView;

use InvalidArgumentException;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\CountableDataInterface;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\FilterableDataInterface;
use Yiisoft\Data\Reader\OffsetableDataInterface;
use Yiisoft\Data\Reader\SortableDataInterface;
use Yiisoft\Factory\Exceptions\InvalidConfigException;
use Yiisoft\Html\Html;
use Yiisoft\View\View;
use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\DataView\Widget\LinkPager;
use Yiisoft\Yii\DataView\Widget\LinkSorter;

/**
 * BaseListView is a base class for widgets displaying data from data provider
 * such as ListView and GridView.
 * It provides features like sorting, paging and also filtering the data.
 * For more details and usage information on BaseListView, see the [guide article on data
 * widgets](guide:output-data-widgets).
 */
abstract class BaseListView extends Widget
{
    /**
     * @var array the HTML attributes for the container tag of the list view.
     * The "tag" element specifies the tag name of the container element and defaults to "div".
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    protected array $options = [];

    /**
     * @var DataReaderInterface|SortableDataInterface|FilterableDataInterface|OffsetableDataInterface|CountableDataInterface
     *     the data provider for the view. This property is required.
     */
    protected $dataReader;
    /**
     * @var \Yiisoft\Data\Paginator\OffsetPaginator|\Yiisoft\Data\Paginator\KeysetPaginator|null
     */
    protected $paginator;
    /**
     * @var LinkPager|null You can use a different widget class by configuring the "class" element.
     */
    protected ?LinkPager $pager = null;
    /**
     * @var LinkSorter|null You can use a different widget class by configuring the "class" element.
     */
    protected ?LinkSorter $sorter = null;
    /**
     * @var string the HTML content to be displayed as the summary of the list view.
     * If you do not want to show the summary, you may set it with an empty string.
     * The following tokens will be replaced with the corresponding values:
     * - `{begin}`: the starting row number (1-based) currently being displayed
     * - `{end}`: the ending row number (1-based) currently being displayed
     * - `{count}`: the number of rows currently being displayed
     * - `{totalCount}`: the total number of rows available
     * - `{page}`: the page number (1-based) current being displayed
     * - `{pageCount}`: the number of pages available
     */
    protected string $summary = 'Showing <b>{begin, number}-{end, number}</b> of <b>{totalCount, number}</b> {totalCount, plural, one{item} other{items}}.';
    /**
     * @var array the HTML attributes for the summary of the list view.
     * The "tag" element specifies the tag name of the summary element and defaults to "div".
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    protected array $summaryOptions = ['class' => 'summary'];
    /**
     * @var bool whether to show an empty list view if {@see $dataReader} returns no data.
     * The default value is false which displays an element according to the {@see $emptyText}
     * and {@see $emptyTextOptions} properties.
     */
    protected bool $showOnEmpty = false;
    /**
     * @var string|false the HTML content to be displayed when {@see $dataReader} does not have any data.
     * When this is set to `false` no extra HTML content will be generated.
     * The default value is the text "No results found." which will be translated to the current
     * application language.
     * @see showOnEmpty
     * @see emptyTextOptions
     */
    protected ?string $emptyText = 'No results found.';
    protected bool $showEmptyText = true;
    /**
     * @var array the HTML attributes for the emptyText of the list view.
     * The "tag" element specifies the tag name of the emptyText element and defaults to "div".
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    protected array $emptyTextOptions = ['class' => 'empty'];
    /**
     * @var string the layout that determines how different sections of the list view should be organized.
     * The following tokens will be replaced with the corresponding section contents:
     * - `{summary}`: the summary section. See {@see renderSummary()}.
     * - `{items}`: the list items. See {@see renderItems()}.
     * - `{sorter}`: the sorter. See {@see renderSorter()}.
     * - `{pager}`: the pager. See {@see renderPager()}.
     */
    protected string $layout = "{summary}\n{items}\n{pager}";
    /**
     * @var WebView
     */
    private WebView $view;
    /**
     * @var Aliases
     */
    private Aliases $aliases;

    public function __construct(WebView $view, Aliases $aliases)
    {
        $this->view = $view;
        $this->aliases = $aliases;
    }

    /**
     * Renders the data models.
     *
     * @return string the rendering result.
     */
    abstract public function renderItems(): string;

    /**
     * @return CountableDataInterface|DataReaderInterface|FilterableDataInterface|OffsetableDataInterface|SortableDataInterface
     */
    public function getDataReader()
    {
        return $this->dataReader;
    }

    /**
     * @param \Yiisoft\Yii\DataView\Widget\LinkPager|null $pager
     * @return self
     */
    public function setPager(?LinkPager $pager): self
    {
        $this->pager = $pager;

        return $this;
    }

    /**
     * @param \Yiisoft\Yii\DataView\Widget\LinkSorter|null $sorter
     * @return self
     */
    public function setSorter(?LinkSorter $sorter): self
    {
        $this->sorter = $sorter;

        return $this;
    }

    protected function getAliases(): Aliases
    {
        return $this->aliases;
    }

    public function showEmptyText(bool $value): self
    {
        $this->showEmptyText = $value;

        return $this;
    }

    /**
     * Initializes the view.
     *
     * @throws InvalidConfigException
     */
    protected function init(): void
    {
        if ($this->dataReader === null) {
            throw new InvalidConfigException('The "dataReader" property must be set.');
        }
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    /**
     * Runs the widget.
     */
    public function run(): string
    {
        $this->init();

        if ($this->showOnEmpty ||
            (
                $this->dataReader instanceof CountableDataInterface &&
                $this->dataReader->count() > 0
            )
        ) {
            $content = preg_replace_callback(
                '/{\\w+}/',
                function ($matches) {
                    $content = $this->renderSection($matches[0]);

                    return $content === false ? $matches[0] : $content;
                },
                $this->layout
            );
        } else {
            $content = $this->renderEmpty();
        }

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        return Html::tag($tag, $content, $options);
    }

    /**
     * Renders a section of the specified name.
     * If the named section is not supported, false will be returned.
     *
     * @param string $name the section name, e.g., `{summary}`, `{items}`.
     * @return string|bool the rendering result of the section, or false if the named section is not supported.
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
                return false;
        }
    }

    /**
     * Renders the HTML content indicating that the list view has no data.
     *
     * @return string the rendering result
     * @see withEmptyText
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
        if (!$this->dataReader instanceof CountableDataInterface) {
            return '';
        }

        $count = $this->dataReader->count();
        if ($count < 1) {
            return '';
        }

        $summaryOptions = $this->summaryOptions;
        $tag = ArrayHelper::remove($summaryOptions, 'tag', 'div');
        $pagination = $this->paginator;

        if ($pagination instanceof OffsetPaginator) {
            $totalCount = $count;
            $begin = $pagination->getCurrentPageSize() * $pagination->getCurrentPageSize() + 1;
            $end = $begin + $count - 1;
            if ($begin > $end) {
                $begin = $end;
            }
            $page = $pagination->getCurrentPage() + 1;
            $pageCount = $pagination->getCurrentPageSize();
            if (($summaryContent = $this->summary) === null) {
                return Html::tag(
                    $tag,
                    $this->formatMessage(
                        'Showing <b>{begin, number}-{end, number}</b> of <b>{totalCount, number}</b> {totalCount, plural, one{item} other{items}}.',
                        [
                            'begin' => $begin,
                            'end' => $end,
                            'count' => $count,
                            'totalCount' => $totalCount,
                            'page' => $page,
                            'pageCount' => $pageCount,
                        ]
                    ),
                    $summaryOptions
                );
            }
        } else {
            $begin = $page = $pageCount = 1;
            $end = $totalCount = $count;
            if (($summaryContent = $this->summary) === null) {
                return Html::tag(
                    $tag,
                    $this->formatMessage(
                        'Total <b>{count, number}</b> {count, plural, one{item} other{items}}.',
                        [
                            'begin' => $begin,
                            'end' => $end,
                            'count' => $count,
                            'totalCount' => $totalCount,
                            'page' => $page,
                            'pageCount' => $pageCount,
                        ]
                    ),
                    $summaryOptions
                );
            }
        }

        return Html::tag(
            $tag,
            $this->formatMessage(
                'Total <b>{count, number}</b> {count, plural, one{item} other{items}}.',
                [
                    'begin' => $begin,
                    'end' => $end,
                    'count' => $count,
                    'totalCount' => $totalCount,
                    'page' => $page,
                    'pageCount' => $pageCount,
                ]
            ),
            $summaryOptions
        );
    }

    protected function formatMessage(string $message, array $arguments = []): string
    {
        return MessageFormatter::formatMessage($message, $arguments);
    }

    /**
     * Renders the pager.
     *
     * @return string the rendering result
     */
    public function renderPager(): string
    {
        if (
            null === $this->pager ||
            null === $this->paginator ||
            (
                $this->dataReader instanceof CountableDataInterface &&
                $this->dataReader->count() < 1
            )
        ) {
            return '';
        }

        $this->pager->setPaginator($this->paginator);

        return $this->pager->run();
    }

    /**
     * Renders the sorter.
     *
     * @return string the rendering result
     */
    public function renderSorter(): string
    {
        $sort = $this->dataReader->getSort();
        if (
            null === $this->sorter ||
            null === $sort ||
            empty($sort->getCriteria()) ||
            (
                $this->dataReader instanceof CountableDataInterface &&
                $this->dataReader->count() < 1
            )
        ) {
            return '';
        }

        $this->sorter->setSort($sort);

        return $this->sorter->run();
    }

    abstract public function getId();

    public function getView(): View
    {
        return $this->view;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function withEmptyText(?string $emptyText): self
    {
        if ($emptyText !== null) {
            $this->emptyText = $emptyText;
        }

        return $this;
    }

    /**
     * @param CountableDataInterface|DataReaderInterface|FilterableDataInterface|OffsetableDataInterface|SortableDataInterface $dataReader
     * @return static
     */
    public function withDataReader($dataReader): self
    {
        $this->dataReader = $dataReader;

        return $this;
    }

    public function withOptions(array $options): self
    {
        $this->options = ArrayHelper::merge($this->options, $options);

        return $this;
    }

    /**
     * @param KeysetPaginator|OffsetPaginator $paginator
     * @return $this
     */
    public function withPaginator($paginator): self
    {
        if ($paginator !== null && !$paginator instanceof KeysetPaginator && !$paginator instanceof OffsetPaginator) {
            throw new InvalidArgumentException(
                sprintf(
                    'Argument "$paginator" must be instance of %s or %s, got %s',
                    OffsetPaginator::class,
                    KeysetPaginator::class,
                    is_object($paginator) ? get_class($paginator) : gettype($paginator)
                )
            );
        }
        $this->paginator = $paginator;

        return $this;
    }
}
