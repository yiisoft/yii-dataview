<?php

namespace Yiisoft\Yii\DataView;

use Yiisoft\Aliases\Aliases;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\OffsetPaginator;
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
     * The "tag" element specifies the tag name of the container element and defaults to "div".
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    protected array $options = [];

    /**
     * @var DataReaderInterface|SortableDataInterface|FilterableDataInterface|OffsetableDataInterface|CountableDataInterface
     * the data provider for the view. This property is required.
     */
    protected $dataReader;
    /**
     * @var OffsetPaginator|null
     */
    protected ?OffsetPaginator $paginator = null;
    /**
     * @var array the configuration for the pager widget. By default, {@see LinkPager} will be
     * used to render the pager. You can use a different widget class by configuring the "class" element.
     * Note that the widget must support the `pagination` property which will be populated with the
     * {@see \yii\data\BaseDataProvider::pagination} value of the {@see $dataReader} and will overwrite
     * this value.
     */
    protected array $pager = [];
    /**
     * @var array the configuration for the sorter widget. By default, {@see LinkSorter} will be
     * used to render the sorter. You can use a different widget class by configuring the "class" element.
     * Note that the widget must support the `sort` property which will be populated with the
     * [[\yii\data\BaseDataProvider::sort|sort]] value of the {@see $dataReader} and will overwrite this value.
     */
    protected array $sorter = [];
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
     * @var MessageFormatterInterface
     */
    private static MessageFormatterInterface $messageFormatter;
    /**
     * @var View
     */
    private static View $view;
    /**
     * @var Aliases
     */
    private static Aliases $aliases;

    public function __construct(MessageFormatterInterface $messageFormatter, View $view, Aliases $aliases)
    {
        self::$messageFormatter = $messageFormatter;
        self::$view = $view;
        self::$aliases = $aliases;
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

    protected function getAliases(): Aliases
    {
        return self::$aliases;
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

    public static function widget(): self
    {
        return new static(self::$messageFormatter, self::$view, self::$aliases);
    }

    /**
     * Runs the widget.
     */
    public function run(): string
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
        $count = $this->dataReader->count();
        if ($count <= 0) {
            return '';
        }

        $summaryOptions = $this->summaryOptions;
        $tag = ArrayHelper::remove($summaryOptions, 'tag', 'div');
        if (($pagination = $this->paginator) !== null) {
            $totalCount = $this->dataReader->count();
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
        $pagination = $this->paginator;
        if ($pagination === null || $this->dataReader->count() <= 0) {
            return '';
        }

        $config = $this->pager;
        $class = ArrayHelper::remove($config, '__class', LinkPager::class);

        /* @var $pager LinkPager */
        $pager = $class::widget();
        $pager->setPaginator($pagination);

        return $pager->run();
    }

    /**
     * Renders the sorter.
     *
     * @return string the rendering result
     */
    public function renderSorter(): string
    {
        $sort = $this->dataReader->getSort();
        if ($sort === null || empty($sort->getCriteria()) || $this->dataReader->count() <= 0) {
            return '';
        }

        $config = $this->sorter;
        $class = ArrayHelper::remove($config, '__class', LinkSorter::class);

        /* @var $pager LinkSorter */
        $pager = $class::widget();
        $pager->setSort($sort);

        return $pager->run();
    }

    abstract public function getId();

    public function getView(): View
    {
        return self::$view;
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

    public function withPaginator(?OffsetPaginator $paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }
}
