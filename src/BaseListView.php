<?php

namespace Yiisoft\Yii\DataView;

use Yiisoft\Aliases\Aliases;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Data\Reader\CountableDataInterface;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\FilterableDataInterface;
use Yiisoft\Data\Reader\OffsetableDataInterface;
use Yiisoft\Data\Reader\SortableDataInterface;
use Yiisoft\Factory\Exceptions\InvalidConfigException;
use Yiisoft\Html\Html;
use Yiisoft\I18n\MessageFormatterInterface;
use Yiisoft\View\View;
use Yiisoft\Yii\DataView\Widget\LinkPager;
use Yiisoft\Yii\DataView\Widget\LinkSorter;

/**
 * BaseListView is a base class for widgets displaying data from data provider
 * such as ListView and GridView.
 * It provides features like sorting, paging and also filtering the data.
 * For more details and usage information on BaseListView, see the [guide article on data
 * widgets](guide:output-data-widgets).
 */
abstract class BaseListView
{
    /**
     * @var array the HTML attributes for the container tag of the list view.
     *            The "tag" element specifies the tag name of the container element and defaults to "div".
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public array $options = [];
    /**
     * @var DataReaderInterface|SortableDataInterface|FilterableDataInterface|OffsetableDataInterface|CountableDataInterface
     *     the data provider for the view. This property is required.
     */
    public $dataReader;
    /**
     * @var \Yiisoft\Data\Paginator\PaginatorInterface
     */
    public $paginator;
    /**
     * @var array the configuration for the pager widget. By default, [[LinkPager]] will be
     *            used to render the pager. You can use a different widget class by configuring the "class" element.
     *            Note that the widget must support the `pagination` property which will be populated with the
     *            [[\yii\data\BaseDataProvider::pagination|pagination]] value of the [[dataReader]] and will overwrite
     *     this value.
     */
    public $pager = [];
    /**
     * @var array the configuration for the sorter widget. By default, [[LinkSorter]] will be
     *            used to render the sorter. You can use a different widget class by configuring the "class" element.
     *            Note that the widget must support the `sort` property which will be populated with the
     *            [[\yii\data\BaseDataProvider::sort|sort]] value of the [[dataReader]] and will overwrite this value.
     */
    public $sorter = [];
    /**
     * @var string the HTML content to be displayed as the summary of the list view.
     *             If you do not want to show the summary, you may set it with an empty string.
     * The following tokens will be replaced with the corresponding values:
     * - `{begin}`: the starting row number (1-based) currently being displayed
     * - `{end}`: the ending row number (1-based) currently being displayed
     * - `{count}`: the number of rows currently being displayed
     * - `{totalCount}`: the total number of rows available
     * - `{page}`: the page number (1-based) current being displayed
     * - `{pageCount}`: the number of pages available
     */
    public $summary = 'Showing <b>{begin, number}-{end, number}</b> of <b>{totalCount, number}</b> {totalCount, plural, one{item} other{items}}.';
    /**
     * @var array the HTML attributes for the summary of the list view.
     *            The "tag" element specifies the tag name of the summary element and defaults to "div".
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $summaryOptions = ['class' => 'summary'];
    /**
     * @var bool whether to show an empty list view if [[dataReader]] returns no data.
     *           The default value is false which displays an element according to the [[emptyText]]
     *           and [[emptyTextOptions]] properties.
     */
    public $showOnEmpty = false;
    /**
     * @var string|false the HTML content to be displayed when [[dataReader]] does not have any data.
     *                   When this is set to `false` no extra HTML content will be generated.
     *                   The default value is the text "No results found." which will be translated to the current
     *     application language.
     * @see showOnEmpty
     * @see emptyTextOptions
     */
    protected ?string $emptyText = 'No results found.';
    private bool $showEmptyText = true;
    /**
     * @var array the HTML attributes for the emptyText of the list view.
     *            The "tag" element specifies the tag name of the emptyText element and defaults to "div".
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $emptyTextOptions = ['class' => 'empty'];
    /**
     * @var string the layout that determines how different sections of the list view should be organized.
     *             The following tokens will be replaced with the corresponding section contents:
     * - `{summary}`: the summary section. See [[renderSummary()]].
     * - `{items}`: the list items. See [[renderItems()]].
     * - `{sorter}`: the sorter. See [[renderSorter()]].
     * - `{pager}`: the pager. See [[renderPager()]].
     */
    public string $layout = "{summary}\n{items}\n{pager}";
    /**
     * @var \Yiisoft\I18n\MessageFormatterInterface
     */
    private static MessageFormatterInterface $messageFormatter;
    /**
     * @var \Yiisoft\View\View
     */
    private static View $view;
    /**
     * @var \Yiisoft\Aliases\Aliases
     */
    private static Aliases $aliases;

    /**
     * @param bool $value
     * @return \Yiisoft\Yii\DataView\BaseListView
     */
    public function showEmptyText(bool $value): self
    {
        $this->showEmptyText = $value;

        return $this;
    }

    /**
     * @return \Yiisoft\Aliases\Aliases
     */
    protected function getAliases(): Aliases
    {
        return self::$aliases;
    }

    /**
     * Renders the data models.
     *
     * @return string the rendering result.
     */
    abstract public function renderItems(): string;

    public function __construct(MessageFormatterInterface $messageFormatter, View $view, Aliases $aliases)
    {
        self::$messageFormatter = $messageFormatter;
        self::$view = $view;
        self::$aliases = $aliases;
    }

    /**
     * Initializes the view.
     */
    public function init(): self
    {
        if ($this->dataReader === null) {
            throw new InvalidConfigException('The "dataReader" property must be set.');
        }
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        return $this;
    }

    /**
     * @return static
     */
    public static function widget(): self
    {
        return new static(self::$messageFormatter, self::$view, self::$aliases);
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        if ($this->showOnEmpty || $this->dataReader->count() > 0) {
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
    public function renderSection($name): string
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
        $count = $this->dataReader->count();
        if ($count <= 0) {
            return '';
        }
        // TODO fix that
        $language = 'language';

        $summaryOptions = $this->summaryOptions;
        $tag = ArrayHelper::remove($summaryOptions, 'tag', 'div');
        if (($pagination = $this->paginator) !== null) {
            $totalCount = $this->dataReader->count();
            $begin = 0; //$pagination->getCurrentPageSize() * $pagination->pageSize + 1;
            $end = $begin + $count - 1;
            if ($begin > $end) {
                $begin = $end;
            }
            $page = 0; //$pagination->getPage() + 1;
            $pageCount = $pagination->getCurrentPageSize();
            if (($summaryContent = $this->summary) === null) {
                return Html::tag(
                    $tag,
                    self::$messageFormatter->format(
                        'Showing <b>{begin, number}-{end, number}</b> of <b>{totalCount, number}</b> {totalCount, plural, one{item} other{items}}.',
                        [
                            'begin' => $begin,
                            'end' => $end,
                            'count' => $count,
                            'totalCount' => $totalCount,
                            'page' => $page,
                            'pageCount' => $pageCount,
                        ],
                        $language
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
                    self::$messageFormatter->format(
                        'Total <b>{count, number}</b> {count, plural, one{item} other{items}}.',
                        [
                            'begin' => $begin,
                            'end' => $end,
                            'count' => $count,
                            'totalCount' => $totalCount,
                            'page' => $page,
                            'pageCount' => $pageCount,
                        ],
                        $language
                    ),
                    $summaryOptions
                );
            }
        }

        return Html::tag(
            $tag,
            self::$messageFormatter->format(
                'Total <b>{count, number}</b> {count, plural, one{item} other{items}}.',
                [
                    'begin' => $begin,
                    'end' => $end,
                    'count' => $count,
                    'totalCount' => $totalCount,
                    'page' => $page,
                    'pageCount' => $pageCount,
                ],
                $language
            ),
            $summaryOptions
        );
    }

    /**
     * Renders the pager.
     *
     * @return string the rendering result
     */
    public function renderPager()
    {
        $pagination = $this->paginator;
        if ($pagination === null || $this->dataReader->count() <= 0) {
            return '';
        }

        $config = $this->pager;
        $class = ArrayHelper::remove($config, '__class', LinkPager::class);

        /* @var $pager LinkPager */
        $pager = $class::widget();
        $pager->paginator = $pagination;

        return $pager->run();
    }

    /**
     * Renders the sorter.
     *
     * @return string the rendering result
     */
    public function renderSorter()
    {
        $sort = $this->dataReader->getSort();
        if ($sort === null || empty($sort->getCriteria()) || $this->dataReader->count() <= 0) {
            return '';
        }
        /* @var $class LinkSorter */
        $sorter = $this->sorter;
        ArrayHelper::remove($sorter, '__class', LinkSorter::class);
        $sorter['sort'] = $sort;
        $sorter['view'] = $this->getView();

        return $sorter::widget();
    }

//    abstract public function getId();
    public function getId(): int
    {
        return rand(1, 10);
    }

    public function getView(): View
    {
        return self::$view;
    }

    public function withEmptyText(?string $emptyText): self
    {
        if ($emptyText !== null) {
            $this->emptyText = $emptyText;
        }

        return $this;
    }

    /**
     * @param \Yiisoft\Data\Reader\CountableDataInterface|\Yiisoft\Data\Reader\DataReaderInterface|\Yiisoft\Data\Reader\FilterableDataInterface|\Yiisoft\Data\Reader\OffsetableDataInterface|\Yiisoft\Data\Reader\SortableDataInterface $dataReader
     * @return static
     */
    public function withDataReader($dataReader): self
    {
        $this->dataReader = $dataReader;

        return $this;
    }

    /**
     * @param array $options
     * @return static
     */
    public function withOptions(array $options): self
    {
        $this->options = ArrayHelper::merge($this->options, $options);

        return $this;
    }

    /**
     * @param $paginator
     * @return static
     */
    public function withPaginator(?PaginatorInterface $paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }
}
