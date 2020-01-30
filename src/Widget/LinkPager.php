<?php

namespace Yiisoft\Yii\DataView\Widget;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Factory\Exceptions\InvalidConfigException;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

/**
 * LinkPager displays a list of hyperlinks that lead to different pages of target.
 * LinkPager works with a [[Pagination]] object which specifies the total number
 * of pages and the current page number.
 * Note that LinkPager only generates the necessary HTML markups. In order for it
 * to look like a real pager, you should provide some CSS styles for it.
 * With the default configuration, LinkPager should look good using Twitter Bootstrap CSS framework.
 * For more details and usage information on LinkPager, see the [guide article on pagination](guide:output-pagination).
 * TODO write tests
 */
class LinkPager extends Widget
{
    /**
     * @var \Yiisoft\Data\Paginator\PaginatorInterface|null the pagination object that this pager is associated with.
     *                 You must set this property in order to make LinkPager work.
     */
    private ?PaginatorInterface $paginator = null;
    /**
     * @var array HTML attributes for the pager container tag.
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    private array $options = ['class' => 'pagination'];
    /**
     * @var array HTML attributes which will be applied to all link containers
     */
    private array $linkContainerOptions = [];
    /**
     * @var array HTML attributes for the link in a pager container tag.
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    private array $linkOptions = [];
    /**
     * @var string the CSS class for the each page button.
     */
    private ?string $pageCssClass;
    /**
     * @var string the CSS class for the "first" page button.
     */
    private string $firstPageCssClass = 'first';
    /**
     * @var string the CSS class for the "last" page button.
     */
    private string $lastPageCssClass = 'last';
    /**
     * @var string the CSS class for the "previous" page button.
     */
    private string $prevPageCssClass = 'prev';
    /**
     * @var string the CSS class for the "next" page button.
     */
    private string $nextPageCssClass = 'next';
    /**
     * @var string the CSS class for the active (currently selected) page button.
     */
    private string $activePageCssClass = 'active';
    /**
     * @var string the CSS class for the disabled page buttons.
     */
    private string $disabledPageCssClass = 'disabled';
    /**
     * @var array the options for the disabled tag to be generated inside the disabled list element.
     *            In order to customize the html tag, please use the tag key.
     * ```php
     * $disabledListItemSubTagOptions = ['tag' => 'div', 'class' => 'disabled-div'];
     * ```
     */
    private array $disabledListItemSubTagOptions = [];
    /**
     * @var int maximum number of page buttons that can be displayed. Defaults to 10.
     */
    private int $maxButtonCount = 10;
    /**
     * @var string|bool the label for the "next" page button. Note that this will NOT be HTML-encoded.
     *                  If this property is false, the "next" page button will not be displayed.
     */
    private $nextPageLabel = '&raquo;';
    /**
     * @var string|bool the text label for the previous page button. Note that this will NOT be HTML-encoded.
     *                  If this property is false, the "previous" page button will not be displayed.
     */
    private $prevPageLabel = '&laquo;';
    /**
     * @var string|bool the text label for the "first" page button. Note that this will NOT be HTML-encoded.
     *                  If it's specified as true, page number will be used as label.
     *                  Default is false that means the "first" page button will not be displayed.
     */
    private $firstPageLabel = false;
    /**
     * @var string|bool the text label for the "last" page button. Note that this will NOT be HTML-encoded.
     *                  If it's specified as true, page number will be used as label.
     *                  Default is false that means the "last" page button will not be displayed.
     */
    private $lastPageLabel = false;
    /**
     * @var bool whether to register link tags in the HTML header for prev, next, first and last page.
     *           Defaults to `false` to avoid conflicts when multiple pagers are used on one page.
     * @see http://www.w3.org/TR/html401/struct/links.html#h-12.1.2
     * @see registerLinkTags()
     */
    private bool $registerLinkTags = false;
    /**
     * @var bool Hide widget when only one page exist.
     */
    private bool $hideOnSinglePage = true;
    /**
     * @var bool whether to render current page button as disabled.
     */
    private bool $disableCurrentPageButton = false;

    /**
     * Initializes the pager.
     */
    protected function init(): void
    {
        if ($this->paginator === null) {
            throw new InvalidConfigException('The "pagination" property must be set.');
        }
    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     *
     * @return string the result of widget execution to be outputted.
     */
    public function run(): string
    {
        $this->init();

        if ($this->registerLinkTags) {
            $this->registerLinkTags();
        }

        return $this->renderPageButtons();
    }

    /**
     * Registers relational link tags in the html header for prev, next, first and last page.
     * These links are generated using [[\yii\data\Pagination::getLinks()]].
     *
     * @see http://www.w3.org/TR/html401/struct/links.html#h-12.1.2
     */
    protected function registerLinkTags(): void
    {
        // TODO fix that
        return;
        $view = $this->getView();
        foreach ($this->paginator->getLinks() as $rel => $href) {
            $view->registerLinkTag(['rel' => $rel, 'href' => $href], $rel);
        }
    }

    /**
     * Renders the page buttons.
     *
     * @return string the rendering result
     */
    protected function renderPageButtons(): string
    {
        $paginator = $this->paginator;
        $pageCount = $paginator->getCurrentPageSize();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }

        $buttons = [];
        $currentPage = $paginator->getCurrentPage();

        // first page
        $firstPageLabel = $this->firstPageLabel === true ? '1' : $this->firstPageLabel;
        if ($firstPageLabel !== false) {
            $disabled = $paginator->isOnFirstPage() || $paginator->withPreviousPageToken(null)->isOnFirstPage();
            $buttons[] = $this->renderPageButton($firstPageLabel, 0, $this->firstPageCssClass, $disabled, false);
        }

        // prev page
        if ($this->prevPageLabel !== false) {
            if (($page = $currentPage - 1) < 0) {
                $page = 0;
            }
            $disabled = $paginator->isOnFirstPage() || $paginator->withPreviousPageToken(null)->isOnFirstPage();
            $buttons[] = $this->renderPageButton(
                $this->prevPageLabel,
                $page,
                $this->prevPageCssClass,
                $disabled,
                false
            );
        }

        // internal pages
        [$beginPage, $endPage] = $this->getPageRange();
        for ($i = $beginPage; $i <= $endPage; $i++) {
            $disabled = $paginator->isOnLastPage() || $paginator->withNextPageToken(null)->isOnLastPage();
            $buttons[] = $this->renderPageButton($i + 1, $i, null, $disabled, $i == $currentPage);
        }

        // next page
        if ($this->nextPageLabel !== false) {
            if (($page = $currentPage + 1) >= $pageCount - 1) {
                $page = $pageCount - 1;
            }
            $disabled = $paginator->isOnLastPage() || $paginator->withNextPageToken(null)->isOnLastPage();
            $buttons[] = $this->renderPageButton(
                $this->nextPageLabel,
                $page,
                $this->nextPageCssClass,
                $disabled,
                false
            );
        }

        // last page
        $lastPageLabel = $this->lastPageLabel === true ? $pageCount : $this->lastPageLabel;
        if ($lastPageLabel !== false) {
            $disabled = $paginator->isOnLastPage() || $paginator->withNextPageToken(null)->isOnLastPage();
            $buttons[] = $this->renderPageButton(
                $lastPageLabel,
                $pageCount - 1,
                $this->lastPageCssClass,
                $disabled,
                false
            );
        }

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'ul');

        return Html::tag($tag, implode("\n", $buttons), $options);
    }

    /**
     * Renders a page button.
     * You may override this method to customize the generation of page buttons.
     *
     * @param string $label the text label for the button
     * @param int $page the page number
     * @param string $class the CSS class for the page button.
     * @param bool $disabled whether this page button is disabled
     * @param bool $active whether this page button is active
     * @return string the rendering result
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active): string
    {
        $options = $this->linkContainerOptions;
        $linkWrapTag = ArrayHelper::remove($options, 'tag', 'li');
        Html::addCssClass($options, empty($class) ? $this->pageCssClass : $class);

        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
            $disabledItemOptions = $this->disabledListItemSubTagOptions;
            $tag = ArrayHelper::remove($disabledItemOptions, 'tag', 'span');

            return Html::tag($linkWrapTag, Html::tag($tag, $label, $disabledItemOptions), $options);
        }
        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;

        return Html::tag(
            $linkWrapTag,
            Html::a($label, $this->paginator->withNextPageToken($page), $linkOptions),
            $options
        );
    }

    /**
     * @return array the begin and end pages that need to be displayed.
     */
    protected function getPageRange(): array
    {
        $currentPage = $this->paginator->getCurrentPage();
        $pageCount = $this->paginator->getTotalPages();

        $beginPage = max(0, $currentPage - (int)($this->maxButtonCount / 2));
        if (($endPage = $beginPage + $this->maxButtonCount - 1) >= $pageCount) {
            $endPage = $pageCount - 1;
            $beginPage = max(0, $endPage - $this->maxButtonCount + 1);
        }

        return [$beginPage, $endPage];
    }

    public function setPaginator(?PaginatorInterface $paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function setLinkContainerOptions(array $linkContainerOptions): self
    {
        $this->linkContainerOptions = $linkContainerOptions;

        return $this;
    }

    public function setLinkOptions(array $linkOptions): self
    {
        $this->linkOptions = $linkOptions;

        return $this;
    }

    public function setPageCssClass(string $pageCssClass): self
    {
        $this->pageCssClass = $pageCssClass;

        return $this;
    }

    public function setFirstPageCssClass(string $firstPageCssClass): self
    {
        $this->firstPageCssClass = $firstPageCssClass;

        return $this;
    }

    public function setLastPageCssClass(string $lastPageCssClass): self
    {
        $this->lastPageCssClass = $lastPageCssClass;

        return $this;
    }

    public function setPrevPageCssClass(string $prevPageCssClass): self
    {
        $this->prevPageCssClass = $prevPageCssClass;

        return $this;
    }

    public function setNextPageCssClass(string $nextPageCssClass): self
    {
        $this->nextPageCssClass = $nextPageCssClass;

        return $this;
    }

    public function setActivePageCssClass(string $activePageCssClass): self
    {
        $this->activePageCssClass = $activePageCssClass;

        return $this;
    }

    public function setDisabledPageCssClass(string $disabledPageCssClass): self
    {
        $this->disabledPageCssClass = $disabledPageCssClass;

        return $this;
    }

    public function setDisabledListItemSubTagOptions(array $disabledListItemSubTagOptions): self
    {
        $this->disabledListItemSubTagOptions = $disabledListItemSubTagOptions;

        return $this;
    }

    public function setMaxButtonCount(int $maxButtonCount): self
    {
        $this->maxButtonCount = $maxButtonCount;

        return $this;
    }

    public function setNextPageLabel($nextPageLabel): self
    {
        $this->nextPageLabel = $nextPageLabel;

        return $this;
    }

    public function setPrevPageLabel($prevPageLabel): self
    {
        $this->prevPageLabel = $prevPageLabel;

        return $this;
    }

    public function setFirstPageLabel($firstPageLabel): self
    {
        $this->firstPageLabel = $firstPageLabel;

        return $this;
    }

    public function setLastPageLabel($lastPageLabel): self
    {
        $this->lastPageLabel = $lastPageLabel;

        return $this;
    }

    public function setRegisterLinkTags(bool $registerLinkTags): self
    {
        $this->registerLinkTags = $registerLinkTags;

        return $this;
    }

    public function setHideOnSinglePage(bool $hideOnSinglePage): self
    {
        $this->hideOnSinglePage = $hideOnSinglePage;

        return $this;
    }

    public function setDisableCurrentPageButton(bool $disableCurrentPageButton): self
    {
        $this->disableCurrentPageButton = $disableCurrentPageButton;

        return $this;
    }
}
