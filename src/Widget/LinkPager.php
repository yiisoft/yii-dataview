<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Widget;

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
    private const BOOTSTRAP = 'bootstrap';
    private const BULMA = 'bulma';
    private const FRAMEWORKCSS = [
        self::BOOTSTRAP,
        self::BULMA,
    ];
    private array $buttonsContainerAttributes = [];
    private array $linkAttributes = ['class' => 'page-link'];
    private array $navAttributes = ['aria-label' => 'Pagination'];
    private array $ulAttributes = ['class' => 'pagination justify-content-center mt-4'];
    private string $activePageCssClass = 'active';
    private string $disabledPageCssClass = 'disabled';
    private string $firstPageCssClass = 'page-item';
    private string $lastPageCssClass = 'page-item';
    private string $nextPageCssClass = 'page-item';
    private string $pageCssClass = 'page-item';
    private string $prevPageCssClass = 'page-item';
    private string $firstPageLabel = '';
    private string $lastPageLabel = '';
    private string $nextPageLabel = 'Next Page';
    private string $prevPageLabel = 'Previous';
    public bool $disableCurrentPageButton = false;
    private string $frameworkCss = self::BOOTSTRAP;
    private bool $hideOnSinglePage = true;
    private int $maxButtonCount = 10;
    private bool $registerLinkTags = false;
    private array $requestAttributes = [];
    private array $requestQueryParams = [];
    private PaginatorInterface $paginator;
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
        $html = '';
        $this->buildWidget();

        if (!isset($this->paginator)) {
            throw new InvalidConfigException('The "paginator" property must be set.');
        }

        if ($this->registerLinkTags) {
            $this->registerLinkTagsInternal();
        }

        return $this->renderPageButtons();
    }

    /**
     * @param string the CSS class for the active (currently selected) page button.
     *
     * @return $this
     */
    public function activePageCssClass(string $activePageCssClass): self
    {
        $new = clone $this;
        $new->activePageCssClass = $activePageCssClass;

        return $new;
    }

    /**
     * @param array $buttonsContainerAttributes HTML attributes which will be applied to all button containers.
     *
     * @return $this
     */
    public function buttonsContainerAttributes(array $buttonsContainerAttributes): self
    {
        $new = clone $this;
        $new->buttonsContainerAttributes = $buttonsContainerAttributes;

        return $new;
    }

    /**
     * @param bool $disableCurrentPageButton whether to render current page button as disabled.
     *
     * @return $this
     */
    public function disableCurrentPageButton(bool $disableCurrentPageButton): self
    {
        $new = clone $this;
        $new->disableCurrentPageButton = $disableCurrentPageButton;

        return $new;
    }

    /**
     * @param string $disabledPageCssClass the CSS class for the disabled page buttons.
     *
     * @return $this
     */
    public function disabledPageCssClass(string $disabledPageCssClass): self
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
    public function firstPageCssClass(string $firstPageCssClass): self
    {
        $new = clone $this;
        $new->firstPageCssClass = $firstPageCssClass;

        return $new;
    }

    /**
     * @param string $firstPageLabel the text label for the "first" page button. Note that this will NOT be
     * HTML-encoded.
     *
     * If it's specified as true, page number will be used as label.
     *
     * Default is false that means the "first" page button will not be displayed.
     *
     * @return $this
     */
    public function firstPageLabel(string $firstPageLabel): self
    {
        $new = clone $this;
        $new->firstPageLabel = $firstPageLabel;

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

    /**
     * @param bool $hideOnSinglePage Hide widget when only one page exist.
     *
     * @return $this
     */
    public function hideOnSinglePage(bool $hideOnSinglePage): self
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
    public function lastPageCssClass(string $lastPageCssClass): self
    {
        $new = clone $this;
        $new->lastPageCssClass = $lastPageCssClass;

        return $new;
    }

    /**
     * @param string $lastPageLabel the text label for the "last" page button. Note that this will NOT be HTML-encoded.
     *
     * If it's specified as true, page number will be used as label.
     *
     * Default is false that means the "last" page button will not be displayed.
     *
     * @return $this
     */
    public function lastPageLabel(string $lastPageLabel): self
    {
        $new = clone $this;
        $new->lastPageLabel = $lastPageLabel;

        return $new;
    }

    /**
     * @param array $linkAttributes HTML attributes for the link in a pager container tag.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function linkAttributes(array $linkAttributes): self
    {
        $new = clone $this;
        $new->linkAttributes = $linkAttributes;

        return $new;
    }

    /**
     * @param int $maxButtonCount maximum number of page buttons that can be displayed. Defaults to 10.
     *
     * @return $this
     */
    public function maxButtonCount(int $maxButtonCount): self
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
    public function nextPageCssClass(string $nextPageCssClass): self
    {
        $new = clone $this;
        $new->nextPageCssClass = $nextPageCssClass;

        return $new;
    }

    /**
     * @param string $nextPageLabel the label for the "next" page button. Note that this will NOT be HTML-encoded.
     *
     * If this property is false, the "next" page button will not be displayed.
     *
     * @return $this
     */
    public function nextPageLabel(string $nextPageLabel): self
    {
        $new = clone $this;
        $new->nextPageLabel = $nextPageLabel;

        return $new;
    }

    /**
     * @param array $navAttributes HTML attributes for the pager container tag.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function navAttributes(array $navAttributes): self
    {
        $new = clone $this;
        $new->navAttributes = $navAttributes;

        return $new;
    }

    /**
     * @param array $ulAttributes HTML attributes for the pager container tag.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function ulAttributes(array $ulAttributes): self
    {
        $new = clone $this;
        $new->ulAttributes = $ulAttributes;

        return $new;
    }

    /**
     * @param string $pageCssClass the CSS class for the each page button.
     *
     * @return $this
     */
    public function pageCssClass(string $pageCssClass): self
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
    public function paginator(PaginatorInterface $paginator): self
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
    public function prevPageCssClass(string $prevPageCssClass): self
    {
        $new = clone $this;
        $new->prevPageCssClass = $prevPageCssClass;

        return $new;
    }

    /**
     * @param string $prevPageLabel the text label for the "previous" page button. Note that this will NOT
     * be HTML-encoded.
     *
     * If this property is false, the "previous" page button will not be displayed.
     *
     * @return $this
     */
    public function prevPageLabel(string $prevPageLabel): self
    {
        $new = clone $this;
        $new->prevPageLabel = $prevPageLabel;

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
        $new = clone $this;
        $new->registerLinkTags = $registerLinkTags;

        return $new;
    }

    /**
     * Registers relational link tags in the html header for prev, next, first and last page.
     *
     * These links are generated using {@see paginator::getLinks()}.
     *
     * @see http://www.w3.org/TR/html401/struct/links.html#h-12.1.2
     */
    private function registerLinkTagsInternal(): void
    {
        /** @var array */
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
     *
     * @psalm-suppress UndefinedInterfaceMethod
     */
    private function renderPageButtons(): string
    {
        $buttons = [];
        $links = [];

        /** @var int */
        $currentPage = $this->paginator->getCurrentPage();

        /** @var int */
        $pageCount = $this->paginator->getTotalPages();

        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }

        $renderFirstPageButtonLink = $this->renderFirstPageButtonLink();
        $renderPreviousPageButtonLink = $this->renderPreviousPageButtonLink($currentPage);
        $renderPageButtonLinks = $this->renderPageButtonLinks($currentPage);
        $renderNextPageButtonLink = $this->renderNextPageButtonLink($currentPage, $pageCount);
        $renderLastPageButtonLink = $this->renderLastPageButtonLink($pageCount);

        /** @psalm-var non-empty-string */
        $tag = ArrayHelper::remove($this->ulAttributes, 'tag', 'ul');

        $html =
            Html::openTag('nav', $this->navAttributes) . "\n" .
                Html::tag(
                    $tag,
                    "\n" .
                    trim($renderFirstPageButtonLink) . trim($renderPreviousPageButtonLink) .
                    "\n" .
                    implode("\n", $renderPageButtonLinks) .
                    "\n" .
                    trim($renderNextPageButtonLink) . trim($renderLastPageButtonLink) .
                    "\n",
                    $this->ulAttributes
                )->encode(false) . "\n" .
            Html::closeTag('nav') . "\n";

        if ($this->frameworkCss === self::BULMA) {
            $html =
                Html::openTag('nav', $this->navAttributes) . "\n" .
                    trim($renderFirstPageButtonLink) . trim($renderPreviousPageButtonLink) .
                    Html::tag($tag, "\n" . implode("\n", $renderPageButtonLinks), $this->ulAttributes) .
                    trim($renderNextPageButtonLink) . trim($renderLastPageButtonLink) . "\n" .
                Html::closeTag('nav');
        }

        return $html;
    }

    /**
     * Renders a page button.
     *
     * You may override this method to customize the generation of page buttons.
     *
     * @param string $label the text label for the button
     * @param int $page the page number
     * @param array $buttonsAttributes the attributes class for the page button.
     * @param bool $disabled whether this page button is disabled
     * @param bool $active whether this page button is active
     *
     * @throws JsonException
     *
     * @return string the rendering result
     */
    private function renderPageButton(
        string $label,
        int $page,
        array $buttonsAttributes,
        bool $disabled = false,
        bool $active = false
    ): string {
        /** @psalm-var non-empty-string */
        $linkWrapTag = ArrayHelper::remove($buttonsAttributes, 'tag', 'li');
        $linkAttributes = $this->linkAttributes;
        $linkAttributes['data-page'] = $page;

        if ($active) {
            Html::addCssClass($buttonsAttributes, $this->activePageCssClass);
        }

        if ($disabled) {
            $linkAttributes['aria-disabled'] = 'true';
            $linkAttributes['tabindex'] = '-1';
        }

        if ($disabled && $this->frameworkCss === self::BOOTSTRAP) {
            Html::addCssClass($buttonsAttributes, $this->disabledPageCssClass);
        }

        if ($disabled && $this->frameworkCss === self::BULMA) {
            $buttonsAttributes['disabled'] = true;
        }

        return Html::tag(
            $linkWrapTag,
            Html::a(
                $label,
                $this->createUrl($page),
                $linkAttributes
            )->render()
        )
        ->attributes($buttonsAttributes)
        ->encode(false)
        ->render();
    }

    /**
     * @return array the begin and end pages that need to be displayed.
     *
     * @psalm-suppress UndefinedInterfaceMethod
     */
    private function getPageRange(): array
    {
        /** @var int */
        $currentPage = $this->paginator->getCurrentPage();

        /** @var int */
        $pageCount = $this->paginator->getTotalPages();

        $beginPage = max(1, $currentPage - (int) ($this->maxButtonCount / 2));

        if (($endPage = $beginPage + $this->maxButtonCount - 1) >= $pageCount) {
            $endPage = $pageCount;
            $beginPage = max(1, $endPage - $this->maxButtonCount + 1);
        }

        return [$beginPage, $endPage];
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
    private function createUrl(int $page, int $pageSize = null): string
    {
        $currentRoute = $this->urlMatcher->getCurrentRoute();
        $url = '';

        $params = array_merge(['page' => $page], $this->requestAttributes, $this->requestQueryParams);

        if ($currentRoute !== null) {
            $action = $currentRoute->getName();
            $url = $this->urlGenerator->generate($action, $params);
        }

        return $url;
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     */
    private function createLinks(): array
    {
        /** @var int */
        $currentPage = $this->paginator->getCurrentPage();

        /** @var int */
        $pageCount = $this->paginator->getTotalPages();

        $links = [self::REL_SELF => $this->createUrl($currentPage, null)];

        if ($pageCount === 1) {
            $links[self::LINK_FIRST] = $this->createUrl(1, null);
            $links[self::LINK_LAST] = $this->createUrl($pageCount, null);
            if ($currentPage > 1) {
                $links[self::LINK_PREV] = $this->createUrl($currentPage, null);
            }
            if ($currentPage < $pageCount) {
                $links[self::LINK_NEXT] = $this->createUrl($currentPage, null);
            }
        }

        return $links;
    }

    private function buildWidget(): void
    {
        if ($this->frameworkCss === self::BULMA) {
            $this->buildBulma();
        }
    }

    private function buildBulma(): void
    {
        $this->navAttributes['class'] = 'pagination is-centered mt-4';
        $this->ulAttributes['class'] = 'pagination-list justify-content-center mt-4';
        $this->linkAttributes = [];
        $this->pageCssClass = 'pagination-link';
        $this->firstPageCssClass = 'pagination-previous';
        $this->lastPageCssClass = 'pagination-next';
        $this->prevPageCssClass = 'pagination-previous has-background-link has-text-white';
        $this->nextPageCssClass = 'pagination-next has-background-link has-text-white';
        $this->activePageCssClass = 'is-current';
        $this->disabledPageCssClass = 'disabled';
    }

    private function renderFirstPageButtonLink(): string
    {
        $html = '';

        if ($this->firstPageLabel !== '') {
            $html = $this->renderPageButton($this->firstPageLabel, 1, ['class' => $this->firstPageCssClass]);
        }

        return $html;
    }

    private function renderPreviousPageButtonLink(int $currentPage): string
    {
        $html = '';

        if ($this->prevPageLabel !== '') {
            $html = $this->renderPageButton(
                $this->prevPageLabel,
                max($currentPage - 1, 1),
                ['class' => $this->prevPageCssClass],
                $currentPage === 1,
            );
        }

        return $html;
    }

    private function renderPageButtonLinks(int $currentPage): array
    {
        $buttons = [];

        /**
         * link buttons pages
         *
         * @var int $beginPage
         * @var int $endPage
         */
        [$beginPage, $endPage] = $this->getPageRange();

        Html::addCssClass($this->buttonsContainerAttributes, $this->pageCssClass);

        for ($i = $beginPage; $i <= $endPage; ++$i) {
            $buttons[] = $this->renderPageButton(
                (string) $i,
                $i,
                $this->buttonsContainerAttributes,
                $this->disableCurrentPageButton && $i === $currentPage,
                $i === $currentPage,
            );
        }

        return $buttons;
    }

    private function renderNextPageButtonLink(int $currentPage, int $pageCount): string
    {
        $html = '';

        if ($this->nextPageLabel !== '') {
            $html = $this->renderPageButton(
                $this->nextPageLabel,
                min($pageCount, $currentPage + 1),
                ['class' => $this->nextPageCssClass],
                $currentPage === $pageCount,
            );
        }

        return $html;
    }

    private function renderLastPageButtonLink(int $pageCount): string
    {
        $html = '';

        if ($this->lastPageLabel !== '') {
            $html = $this->renderPageButton($this->lastPageLabel, $pageCount, ['class' => $this->lastPageCssClass]);
        }

        return $html;
    }
}
