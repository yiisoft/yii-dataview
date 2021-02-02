<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use JsonException;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Factory\Exceptions\InvalidConfigException;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\DataView\Factory\GridViewFactory;
use Yiisoft\Yii\DataView\Widget\Bulma\LinkPager;
use Yiisoft\Yii\DataView\Widget\LinkSorter;

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
        '{totalCount, plural, one{item} other{items}}.';
    protected array $summaryOptions = ['class' => 'summary'];
    protected bool $showOnEmpty = false;
    protected ?string $emptyText = 'No results found.';
    protected bool $showEmptyText = true;
    protected array $emptyTextOptions = ['class' => 'empty'];
    protected string $layout = "{summary}\n{items}\n{pager}";
    private WebView $view;
    private Aliases $aliases;
    private string $linkPagerClass = LinkPager::class;
    protected GridViewFactory $gridViewFactory;
    protected TranslatorInterface $translator;

    public function __construct(
        Aliases $aliases,
        GridViewFactory $gridViewFactory,
        TranslatorInterface $translator,
        PaginatorInterface $paginator,
        WebView $view
    ) {
        $this->aliases = $aliases;
        $this->gridViewFactory = $gridViewFactory;
        $this->translator = $translator;
        $this->paginator = $paginator;
        $this->view = $view;
    }

    /**
     * Renders the data models.
     *
     * @return string the rendering result.
     */
    abstract public function renderItems(): string;

    public function sorter(?LinkSorter $sorter): self
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
     * Runs the widget.
     */
    public function run(): string
    {
        $this->init();

        if ($this->paginator === null) {
            throw new InvalidConfigException('The "PaginatorInterface::class" property must be set.');
        }

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

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

        return Html::tag($tag, $content, $options);
    }

    /**
     * Renders a section of the specified name.
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

        if ($count < 1) {
            return '';
        }

        $summaryOptions = $this->summaryOptions;
        $tag = ArrayHelper::remove($summaryOptions, 'tag', 'div');
        $pagination = $this->paginator;

        if ($pagination instanceof OffsetPaginator) {
            $totalCount = $count;
            $begin = $pagination->getOffset() + 1;
            $end = ($begin + $pagination->getPageSize()) - 1;

            if ($begin > $end) {
                $begin = $end;
            }

            $page = $pagination->getCurrentPage() + 1;
            $pageCount = $pagination->getCurrentPageSize();

            if (($summaryContent = $this->summary) === null) {
                return Html::tag(
                    $tag,
                    $this->translator->translate(
                        $this->summary,
                        [
                            'begin' => $begin,
                            'end' => $end,
                            'count' => $count,
                            'totalCount' => $totalCount,
                            'page' => $page,
                            'pageCount' => $pageCount,
                        ],
                        'user',
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
                    $this->translator->translate(
                        $this->summary,
                        [
                            'begin' => $begin,
                            'end' => $end,
                            'count' => $count,
                            'totalCount' => $totalCount,
                            'page' => $page,
                            'pageCount' => $pageCount,
                        ],
                        'user',
                    ),
                    $summaryOptions
                );
            }
        }

        return Html::tag(
            $tag,
            $this->translator->translate(
                $this->summary,
                [
                    'begin' => $begin,
                    'end' => $end,
                    'count' => $count,
                    'totalCount' => $totalCount,
                    'page' => $page,
                    'pageCount' => $pageCount,
                ],
                'user',
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
        if ($this->paginator === null || $this->paginator->getTotalItems() < 1) {
            return '';
        }

        /** @var $class LinkPager */
        $pager = $this->linkPagerClass::widget();

        return $pager
            ->paginator($this->paginator)
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

    abstract public function getId(): string;

    public function getView(): WebView
    {
        return $this->view;
    }

    public function getOptions(): array
    {
        return $this->options;
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
        if ($emptyText !== null) {
            $this->emptyText = $emptyText;
        }

        return $this;
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
        $this->options = ArrayHelper::merge($this->options, $options);

        return $this;
    }

    public function linkPagerClass(string $linkPagerClass): self
    {
        $this->linkPagerClass = $linkPagerClass;

        return $this;
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
    public function summaryOptions(array $summaryOptions): self
    {
        $this->summaryOptions = $summaryOptions;

        return $this;
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
        $this->showOnEmpty = $showOnEmpty;

        return $this;
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
        $this->emptyTextOptions = $emptyTextOptions;

        return $this;
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
        $this->layout = $layout;

        return $this;
    }

    public function getPaginator(): PaginatorInterface
    {
        return $this->paginator;
    }
}
