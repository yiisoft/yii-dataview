<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Widget\Bootstrap5;

use JsonException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\DataView\Exception\InvalidConfigException;

use function implode;

/**
 * LinkPager displays a list of hyperlinks that lead to different pages of target.
 *
 * LinkPager works with a {@see paginator} object which specifies the total number of pages and the current page
 * number.
 *
 * Note that LinkPager only generates the necessary HTML markups. In order for it to look like a real pager, you
 * should provide some CSS styles for it.
 *
 * With the default configuration, LinkPager should look good using Twitter Bootstrap CSS framework.
 *
 * For more details and usage information on LinkPager, see the [guide article on paginator](guide:output-paginator).
 */
final class LinkPager extends Widget
{
    private const REL_SELF = 'self';
    private const LINK_NEXT = 'next';
    private const LINK_PREV = 'prev';
    private const LINK_FIRST = 'first';
    private const LINK_LAST = 'last';
    private PaginatorInterface $paginator;
    private array $optionsNav = [];
    private array $optionsUl = ['class' => 'pagination justify-content-center mt-4'];
    private array $linkContainerOptions = [];
    private array $linkOptions = ['class' => 'page-link'];
    private string $pageCssClass = 'page-item';
    private string $firstPageCssClass = 'page-item';
    private string $lastPageCssClass = 'page-item';
    private string $prevPageCssClass = 'nav-link';
    private string $nextPageCssClass = 'nav-link';
    private string $activePageCssClass = 'active';
    private string $disabledPageCssClass = 'disabled';
    private array $disabledListItemSubTagOptions = [];
    private int $maxButtonCount = 10;
    private ?string $nextPageLabel = 'Next Page';
    private ?string $prevPageLabel = 'Previous';
    private ?string $firstPageLabel = null;
    private ?string $lastPageLabel = null;
    private bool $registerLinkTags = false;
    private bool $hideOnSinglePage = true;
    public bool $disableCurrentPageButton = false;
    private array $buttonsContainerOptions = [];
    private UrlGeneratorInterface $urlGenerator;
    private UrlMatcherInterface $urlMatcher;
    private WebView $webView;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        UrlMatcherInterface $urlMatcher,
        WebView $webView
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->urlMatcher = $urlMatcher;
        $this->webView = $webView;
    }

    /**
     * Executes the widget.
     *
     * This overrides the parent implementation by displaying the generated page buttons.
     *
     * @throws InvalidConfigException|JsonException
     *
     * @return string
     */
    public function run(): string
    {
        if ($this->paginator === null) {
            throw new InvalidConfigException('The "paginator" property must be set.');
        }

        if ($this->registerLinkTags) {
            $this->registerLinkTagsInternal();
        }

        return $this->renderPageButtons();
    }

    /**
     * Registers relational link tags in the html header for prev, next, first and last page.
     *
     * These links are generated using {@see paginator::getLinks()}.
     *
     * @see http://www.w3.org/TR/html401/struct/links.html#h-12.1.2
     */
    protected function registerLinkTagsInternal(): void
    {
        foreach ($this->createLinks() as $rel => $href) {
            $this->webView->registerLinkTag(['rel' => $rel, 'href' => $href]);
        }
    }

    /**
     * Renders the page buttons.
     *
     * @throws JsonException
     *
     * @return string the rendering result
     */
    protected function renderPageButtons(): string
    {
        $buttons = [];
        $paginator = $this->paginator;
        $currentPage = $paginator->getCurrentPage();
        $pageCount = $paginator->getTotalPages();

        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }

        /* button first page */
        if ($this->firstPageLabel !== null) {
            $linkOptions = $this->linkOptions;
            $linkOptions['data-page'] = 1;

            Html::addCssClass($linkOptions, $this->firstPageCssClass);

            $buttons[] = Html::a($this->firstPageLabel, $this->createUrl(1), $linkOptions);
        }

        /* button previous page */
        if ($this->prevPageLabel !== null) {
            $prevPageLabelOptions = [];
            Html::addCssClass($prevPageLabelOptions, $this->prevPageCssClass);

            if ($currentPage === 1) {
                $prevPage = 1;
                Html::addCssClass($prevPageLabelOptions, $this->disabledPageCssClass);
            } else {
                $prevPage = $currentPage - 1;
            }

            $prevPageLabelOptions['data-page'] = $prevPage;
            $buttons[] = Html::a($this->prevPageLabel, $this->createUrl($prevPage), $prevPageLabelOptions);
        }

        /* buttons pages */
        [$beginPage, $endPage] = $this->getPageRange();

        for ($i = $beginPage; $i <= $endPage; ++$i) {
            $buttons[] = $this->renderPageButton(
                (string) $i,
                $i,
                null,
                $this->disableCurrentPageButton && $i === $currentPage,
                $i === $currentPage,
            );
        }

        /* button next page */
        if ($this->nextPageLabel !== null) {
            $nextPageLabelOptions = [];
            Html::addCssClass($nextPageLabelOptions, $this->nextPageCssClass);

            if ($currentPage === $pageCount) {
                $nextPage = $pageCount;
                Html::addCssClass($nextPageLabelOptions, $this->disabledPageCssClass);
            } else {
                $nextPage = $currentPage + 1;
            }

            $nextPageLabelOptions['data-page'] = $nextPage;
            $buttons[] = Html::a($this->nextPageLabel, $this->createUrl($nextPage), $nextPageLabelOptions);
        }

        /* button last page */
        if ($this->lastPageLabel !== null) {
            $linkOptions = $this->linkOptions;
            $linkOptions['data-page'] = $pageCount;

            Html::addCssClass($linkOptions, $this->lastPageCssClass);

            $buttons[] = Html::a($this->lastPageLabel, $this->createUrl($pageCount), $linkOptions);
        }

        $tag = ArrayHelper::remove($this->optionsUl, 'tag', 'ul');

        return Html::tag(
            'nav',
            Html::tag($tag, implode("\n", $buttons), $this->optionsUl),
            $this->optionsNav
        );
    }

    /**
     * Renders a page button.
     *
     * You may override this method to customize the generation of page buttons.
     *
     * @param string $label the text label for the button
     * @param int $page the page number
     * @param string|null $class the CSS class for the page button.
     * @param bool $disabled whether this page button is disabled
     * @param bool $active whether this page button is active
     *
     * @throws JsonException
     *
     * @return string the rendering result
     */
    protected function renderPageButton(string $label, int $page, ?string $class, bool $disabled, bool $active): string
    {
        $options = $this->buttonsContainerOptions;

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
            Html::a(
                $label,
                $this->createUrl($page),
                $linkOptions
            ),
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

        $beginPage = max(1, $currentPage - (int) ($this->maxButtonCount / 2));

        if (($endPage = $beginPage + $this->maxButtonCount - 1) >= $pageCount) {
            $endPage = $pageCount;
            $beginPage = max(1, $endPage - $this->maxButtonCount + 1);
        }

        return [$beginPage, $endPage];
    }

    /**
     * @param string the CSS class for the active (currently selected) page button.
     *
     * @return $this
     */
    public function activePageCssClass(string $activePageCssClass): self
    {
        $this->activePageCssClass = $activePageCssClass;

        return $this;
    }

    /**
     * @param array $buttonsContainerOptions HTML attributes which will be applied to all button containers.
     *
     * @return $this
     */
    public function buttonsContainerOptions(array $buttonsContainerOptions): self
    {
        $this->buttonsContainerOptions = $buttonsContainerOptions;

        return $this;
    }

    /**
     * @param bool $disableCurrentPageButton whether to render current page button as disabled.
     *
     * @return $this
     */
    public function disableCurrentPageButton(bool $disableCurrentPageButton): self
    {
        $this->disableCurrentPageButton = $disableCurrentPageButton;

        return $this;
    }

    /**
     * @param array $disabledListItemSubTagOptions the options for the disabled tag to be generated inside the disabled
     * list element.
     *
     * In order to customize the html tag, please use the tag key.
     *
     * ```php
     * $disabledListItemSubTagOptions = ['tag' => 'div', 'class' => 'disabled-div'];
     * ```
     *
     * @return $this
     */
    public function disabledListItemSubTagOptions(array $disabledListItemSubTagOptions): self
    {
        $this->disabledListItemSubTagOptions = $disabledListItemSubTagOptions;

        return $this;
    }

    /**
     * @param string $disabledPageCssClass the CSS class for the disabled page buttons.
     *
     * @return $this
     */
    public function disabledPageCssClass(string $disabledPageCssClass): self
    {
        $this->disabledPageCssClass = $disabledPageCssClass;

        return $this;
    }

    /**
     * @param string $firstPageCssClass the CSS class for the "first" page button.
     *
     * @return $this
     */
    public function firstPageCssClass(string $firstPageCssClass): self
    {
        $this->firstPageCssClass = $firstPageCssClass;

        return $this;
    }

    /**
     * @param string|null $firstPageLabel the text label for the "first" page button. Note that this will NOT be
     * HTML-encoded.
     *
     * If it's specified as true, page number will be used as label.
     *
     * Default is false that means the "first" page button will not be displayed.
     *
     * @return $this
     */
    public function firstPageLabel(?string $firstPageLabel): self
    {
        $this->firstPageLabel = $firstPageLabel;

        return $this;
    }

    /**
     * @param bool $hideOnSinglePage Hide widget when only one page exist.
     *
     * @return $this
     */
    public function hideOnSinglePage(bool $hideOnSinglePage): self
    {
        $this->hideOnSinglePage = $hideOnSinglePage;

        return $this;
    }

    /**
     * @param string $lastPageCssClass the CSS class for the "last" page button.
     *
     * @return $this
     */
    public function lastPageCssClass(string $lastPageCssClass): self
    {
        $this->lastPageCssClass = $lastPageCssClass;

        return $this;
    }

    /**
     * @param string|null $lastPageLabel the text label for the "last" page button. Note that this will NOT be
     * HTML-encoded.
     *
     * If it's specified as true, page number will be used as label.
     *
     * Default is false that means the "last" page button will not be displayed.
     *
     * @return $this
     */
    public function lastPageLabel(?string $lastPageLabel): self
    {
        $this->lastPageLabel = $lastPageLabel;

        return $this;
    }

    /**
     * @param array $linkOptions HTML attributes for the link in a pager container tag.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function linkOptions(array $linkOptions): self
    {
        $this->linkOptions = $linkOptions;

        return $this;
    }

    /**
     * @param int $maxButtonCount maximum number of page buttons that can be displayed. Defaults to 10.
     *
     * @return $this
     */
    public function maxButtonCount(int $maxButtonCount): self
    {
        $this->maxButtonCount = $maxButtonCount;

        return $this;
    }

    /**
     * @param string $nextPageCssClass the CSS class for the "next" page button.
     *
     * @return $this
     */
    public function nextPageCssClass(string $nextPageCssClass): self
    {
        $this->nextPageCssClass = $nextPageCssClass;

        return $this;
    }

    /**
     * @param string|null $nextPageLabel the label for the "next" page button. Note that this will NOT be HTML-encoded.
     *
     * If this property is false, the "next" page button will not be displayed.
     *
     * @return $this
     */
    public function nextPageLabel(?string $nextPageLabel): self
    {
        $this->nextPageLabel = $nextPageLabel;

        return $this;
    }

    /**
     * @param array $optionsNav HTML attributes for the pager container tag.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function optionsNav(array $optionsNav): self
    {
        $this->optionsNav = $optionsNav;

        return $this;
    }

    /**
     * @param array $optionsUl HTML attributes for the pager container tag.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function optionsUl(array $optionsUl): self
    {
        $this->optionsUl = $optionsUl;

        return $this;
    }

    /**
     * @param string $pageCssClass the CSS class for the each page button.
     *
     * @return $this
     */
    public function pageCssClass(string $pageCssClass): self
    {
        $this->pageCssClass = $pageCssClass;

        return $this;
    }

    /**
     * @param PaginatorInterface $paginator the paginator object that this pager is associated with.
     *
     * @return $this
     *
     * You must set this property in order to make LinkPager work.
     */
    public function paginator(PaginatorInterface $paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * @param string $prevPageCssClass the CSS class for the "previous" page button.
     *
     * @return $this
     */
    public function prevPageCssClass(string $prevPageCssClass): self
    {
        $this->prevPageCssClass = $prevPageCssClass;

        return $this;
    }

    /**
     * @param string|null $prevPageLabel the text label for the "previous" page button. Note that this will NOT
     * be HTML-encoded.
     *
     * If this property is false, the "previous" page button will not be displayed.
     *
     * @return $this
     */
    public function prevPageLabel(?string $prevPageLabel): self
    {
        $this->prevPageLabel = $prevPageLabel;

        return $this;
    }

    /**
     * @param array $linkContainerOptions HTML attributes which will be applied to all link containers.
     *
     * @return $this
     */
    public function linkContainerOptions(array $linkContainerOptions): self
    {
        $this->linkContainerOptions = $linkContainerOptions;

        return $this;
    }

    /**
     * @param bool $registerLinkTags whether to register link tags in the HTML header for prev, next, first and last
     * page.
     *
     * Defaults to `false` to avoid conflicts when multiple pagers are used on one page.
     *
     * @return $this
     *
     * @see http://www.w3.org/TR/html401/struct/links.html#h-12.1.2
     * @see registerLinkTags()
     */
    public function registerLinkTags(bool $registerLinkTags): self
    {
        $this->registerLinkTags = $registerLinkTags;

        return $this;
    }

    /**
     * Creates the URL suitable for pagination with the specified page number. This method is mainly called by pagers
     * when creating URLs used to perform pagination.
     *
     * @param int $page the zero-based page number that the URL should point to.
     * @param int|null $pageSize the number of items on each page. If not set, the value of {@see pageSize} will be
     * used.
     * @param bool $absolute whether to create an absolute URL. Defaults to `false`.
     *
     * @return string the created URL.
     *
     * {@see params}
     * {@see forcePageParam}
     */
    public function createUrl(int $page, int $pageSize = null, bool $absolute = false): string
    {
        $currentRoute = $this->urlMatcher->getCurrentRoute();
        $url = '';

        if ($currentRoute !== null) {
            $action = $currentRoute->getName();
            $url = $this->urlGenerator->generate($action, ['page' => $page]);

            if ($absolute === true) {
                $url = $this->urlGenerator->generateAbsolute($action, ['page' => $page]);
            }
        }

        return $url;
    }

    private function createLinks(bool $absolute = false): array
    {
        $paginator = $this->paginator;
        $currentPage = $paginator->getCurrentPage();
        $pageCount = $paginator->getTotalPages();

        $links = [self::REL_SELF => $this->createUrl($currentPage, null, $absolute)];

        if ($pageCount = 1) {
            $links[self::LINK_FIRST] = $this->createUrl(1, null, $absolute);
            $links[self::LINK_LAST] = $this->createUrl($pageCount, null, $absolute);
            if ($currentPage > 1) {
                $links[self::LINK_PREV] = $this->createUrl($currentPage, null, $absolute);
            }
            if ($currentPage < $pageCount) {
                $links[self::LINK_NEXT] = $this->createUrl($currentPage, null, $absolute);
            }
        }

        return $links;
    }
}
