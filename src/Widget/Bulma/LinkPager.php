<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Widget\Bulma;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\DataView\Exception\InvalidConfigException;

/**
 * LinkPager displays a list of hyperlinks that lead to different pages of target.
 *
 * LinkPager works with a {@see Pagination} object which specifies the total number of pages and the current page
 * number.
 *
 * Note that LinkPager only generates the necessary HTML markups. In order for it to look like a real pager, you
 * should provide some CSS styles for it.
 *
 * With the default configuration, LinkPager should look good using Twitter Bootstrap CSS framework.
 *
 * For more details and usage information on LinkPager, see the [guide article on pagination](guide:output-pagination).
 */
final class LinkPager extends Widget
{
    private const REL_SELF = 'self';
    private const LINK_NEXT = 'next';
    private const LINK_PREV = 'prev';
    private const LINK_FIRST = 'first';
    private const LINK_LAST = 'last';
    private PaginatorInterface $paginator;
    private $optionsNav = ['class' => 'pagination is-centered mt-4'];
    private $optionsUl = ['class' => 'pagination-list'];
    private array $linkOptions = [];
    private string $pageCssClass = 'pagination-link';
    private string $firstPageCssClass = 'pagination-previous';
    private string $lastPageCssClass = 'pagination-next';
    private string $prevPageCssClass = 'pagination-previous has-background-link has-text-white';
    private string $nextPageCssClass = 'pagination-next has-background-link has-text-white';
    private string $activePageCssClass = 'is-current';
    private string $disabledPageCssClass = 'disabled';
    private array $disabledListItemSubTagOptions = [];
    private int $maxButtonCount = 10;
    private ?string $nextPageLabel = 'Next Page';
    private ?string $prevPageLabel = 'Previous';
    private ?string $firstPageLabel = null;
    private ?string $lastPageLabel = null;
    private bool $registerLinkTags = true;
    private bool $hideOnSinglePage = true;
    private bool $disableCurrentPageButton = false;
    private array $buttonsContainerOptions = [];
    private ?ServerRequestInterface $serverRequest = null;
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
     */
    public function run(): string
    {
        if ($this->paginator === null) {
            throw new InvalidConfigException('The "PaginatorInterface::class" property must be set.');
        }

        if ($this->registerLinkTags) {
            $this->registerLinkTagsInternal();
        }

        return $this->renderPageButtons();
    }

    /**
     * Registers relational link tags in the html header for prev, next, first and last page.
     *
     * These links are generated using {@see Pagination::getLinks()}.
     *
     * {@see http://www.w3.org/TR/html401/struct/links.html#h-12.1.2}
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
     * @return string the rendering result
     */
    protected function renderPageButtons(): string
    {
        $buttons = [];
        $links = [];
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

            $links[] = Html::a($this->firstPageLabel, $this->createUrl(1), $linkOptions);
        }

        /* button previous page */
        if ($this->prevPageLabel !== null) {
            $prevPageLabelOptions = [];
            Html::addCssClass($prevPageLabelOptions, $this->prevPageCssClass);

            if ($currentPage === 1) {
                $prevPage = 1;
                $prevPageLabelOptions = array_merge($prevPageLabelOptions, [$this->disabledPageCssClass => true]);
            } else {
                $prevPage = $currentPage - 1;
            }

            $prevPageLabelOptions['data-page'] = $prevPage;
            $links[] = Html::a($this->prevPageLabel, $this->createUrl($prevPage), $prevPageLabelOptions);
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
                $nextPageLabelOptions = array_merge($nextPageLabelOptions, [$this->disabledPageCssClass => true]);
            } else {
                $nextPage = $currentPage + 1;
            }

            $nextPageLabelOptions['data-page'] = $nextPage;
            $links[] = Html::a($this->nextPageLabel, $this->createUrl($nextPage), $nextPageLabelOptions);
        }

        /* button last page */
        if ($this->lastPageLabel !== null) {
            $linkOptions = $this->linkOptions;
            $linkOptions['data-page'] = $pageCount;

            Html::addCssClass($linkOptions, $this->lastPageCssClass);

            $links[] = Html::a($this->lastPageLabel, $this->createUrl($pageCount), $linkOptions);
        }

        $tag = ArrayHelper::remove($this->optionsUl, 'tag', 'ul');
        $optionsUl = array_merge($this->optionsUl, ['encode' => false]);
        $optionsNav = array_merge($this->optionsNav, ['encode' => false]);

        return Html::tag(
            'nav',
            implode("\n", $links) . Html::tag($tag, implode("\n", $buttons), $optionsUl),
            $optionsNav
        );
    }

    /**
     * Renders a page button.
     *
     * You may override this method to customize the generation of page buttons.
     *
     * @param string $label the text label for the button
     * @param int $page the page number
     * @param string $class the CSS class for the page button.
     * @param bool $disabled whether this page button is disabled
     * @param bool $active whether this page button is active
     *
     * @return string the rendering result
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active): string
    {
        $options = $this->buttonsContainerOptions;
        $options['encode'] = false;

        $linkWrapTag = ArrayHelper::remove($options, 'tag', 'li');

        Html::addCssClass($options, empty($class) ? $this->pageCssClass : $class);

        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }

        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
            $disabledItemOptions = $this->disabledListItemSubTagOptions;
            $disabledItemOptions['encode'] = false;
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
    public function withActivePageCssClass(string $activePageCssClass): self
    {
        $new = clone $this;
        $new->activePageCssClass = $activePageCssClass;

        return $new;
    }

    /**
     * @param array $buttonsContainerOptions HTML attributes which will be applied to all button containers.
     *
     * @return $this
     */
    public function withButtonsContainerOptions(array $buttonsContainerOptions): self
    {
        $new = clone $this;
        $new->buttonsContainerOptions = $buttonsContainerOptions;

        return $new;
    }

    /**
     * @param bool $disableCurrentPageButton whether to render current page button as disabled.
     *
     * @return $this
     */
    public function withDisableCurrentPageButton(bool $disableCurrentPageButton): self
    {
        $new = clone $this;
        $new->disableCurrentPageButton = $disableCurrentPageButton;

        return $new;
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
    public function withDisabledListItemSubTagOptions(array $disabledListItemSubTagOptions): self
    {
        $new = clone $this;
        $new->disabledListItemSubTagOptions = $disabledListItemSubTagOptions;

        return $new;
    }

    /**
     * @param string $disabledPageCssClass the CSS class for the disabled page buttons.
     *
     * @return $this
     */
    public function withDisabledPageCssClass(string $disabledPageCssClass): self
    {
        $new = clone $this;
        $new->disabledPageCssClass = $disabledPageCssClass;

        return $new;
    }

    /**
     * @param string $firstPageCssClass the CSS class for the "first" page button.
     *
     * @return $this
     */
    public function withFirstPageCssClass(string $firstPageCssClass): self
    {
        $new = clone $this;
        $new->firstPageCssClass = $firstPageCssClass;

        return $new;
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
    public function withFirstPageLabel(?string $firstPageLabel): self
    {
        $new = clone $this;
        $new->firstPageLabel = $firstPageLabel;

        return $new;
    }

    /**
     * @param bool $hideOnSinglePage Hide widget when only one page exist.
     *
     * @return $this
     */
    public function withHideOnSinglePage(bool $hideOnSinglePage): self
    {
        $new = clone $this;
        $new->hideOnSinglePage = $hideOnSinglePage;

        return $new;
    }

    /**
     * @param string $lastPageCssClass the CSS class for the "last" page button.
     *
     * @return $this
     */
    public function withLastPageCssClass(string $lastPageCssClass): self
    {
        $new = clone $this;
        $new->lastPageCssClass = $lastPageCssClass;

        return $new;
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
    public function withLastPageLabel(?string $lastPageLabel): self
    {
        $new = clone $this;
        $new->lastPageLabel = $lastPageLabel;

        return $new;
    }

    /**
     * @param array $linkOptions HTML attributes for the link in a pager container tag.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function withLinkOptions(array $linkOptions): self
    {
        $new = clone $this;
        $new->linkOptions = $linkOptions;

        return $new;
    }

    /**
     * @param int $maxButtonCount maximum number of page buttons that can be displayed. Defaults to 10.
     *
     * @return $this
     */
    public function withMaxButtonCount(int $maxButtonCount): self
    {
        $new = clone $this;
        $new->maxButtonCount = $maxButtonCount;

        return $new;
    }

    /**
     * @param string $nextPageCssClass the CSS class for the "next" page button.
     *
     * @return $this
     */
    public function withNextPageCssClass(string $nextPageCssClass): self
    {
        $new = clone $this;
        $new->nextPageCssClass = $nextPageCssClass;

        return $new;
    }

    /**
     * @param string|null $nextPageLabel the label for the "next" page button. Note that this will NOT be HTML-encoded.
     *
     * If this property is false, the "next" page button will not be displayed.
     *
     * @return $this
     */
    public function withNextPageLabel(?string $nextPageLabel): self
    {
        $new = clone $this;
        $new->nextPageLabel = $nextPageLabel;

        return $new;
    }

    /**
     * @param array $optionsNav HTML attributes for the pager container tag.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function withOptionsNav(array $optionsNav): self
    {
        $new = clone $this;
        $new->optionsNav = $optionsNav;

        return $new;
    }

    /**
     * @param array $optionsUl HTML attributes for the pager container tag.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function withOptionsUl(array $optionsUl): self
    {
        $new = clone $this;
        $new->optionsUl = $optionsUl;

        return $new;
    }

    /**
     * @param string $pageCssClass the CSS class for the each page button.
     *
     * @return $this
     */
    public function withPageCssClass(string $pageCssClass): self
    {
        $new = clone $this;
        $new->pageCssClass = $pageCssClass;

        return $new;
    }

    /**
     * @param PaginatorInterface $paginator the paginator object that this pager is associated with.
     *
     * @return $this
     *
     * You must set this property in order to make LinkPager work.
     */
    public function withPaginator(PaginatorInterface $paginator): self
    {
        $new = clone $this;
        $new->paginator = $paginator;

        return $new;
    }

    /**
     * @param string $prevPageCssClass the CSS class for the "previous" page button.
     *
     * @return $this
     */
    public function withPrevPageCssClass(string $prevPageCssClass): self
    {
        $new = clone $this;
        $new->prevPageCssClass = $prevPageCssClass;

        return $new;
    }

    /**
     * @param string|null $prevPageLabel the text label for the "previous" page button. Note that this will NOT
     * be HTML-encoded.
     *
     * If this property is false, the "previous" page button will not be displayed.
     *
     * @return $this
     */
    public function withPrevPageLabel(?string $prevPageLabel): self
    {
        $new = clone $this;
        $new->prevPageLabel = $prevPageLabel;

        return $new;
    }

    /**
     * @param array $linkContainerOptions HTML attributes which will be applied to all link containers.
     *
     * @return $this
     */
    public function withLinkContainerOptions(array $linkContainerOptions): self
    {
        $new = clone $this;
        $new->linkContainerOptions = $linkContainerOptions;

        return $new;
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
    public function withRegisterLinkTags(bool $registerLinkTags): self
    {
        $new = clone $this;
        $new->registerLinkTags = $registerLinkTags;

        return $new;
    }

    public function withServerRequest(ServerRequestInterface $serverRequest): self
    {
        $new = clone $this;
        $new->serverRequest = $serverRequest;

        return $new;
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

        $params = ['page' => $page];

        if ($this->serverRequest !== null) {
            $params = array_merge(
                $this->serverRequest->getQueryParams(),
                ['page' => $page],
            );
        }

        if ($currentRoute !== null) {
            $action = $currentRoute->getName();
            $url = $this->urlGenerator->generate($action, $params);

            if ($absolute === true) {
                $url = $this->urlGenerator->generateAbsolute($action, $params);
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
