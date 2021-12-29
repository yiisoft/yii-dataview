<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Widget;

use JsonException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Link;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Exception\InvalidConfigException;
use Yiisoft\Yii\DataView\Widget;

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
    private const CSS_FRAMEWORKS = [
        self::BOOTSTRAP,
        self::BULMA,
    ];
    private array $buttonsContainerAttributes = [
        'class' => 'page-item',
    ];
    private array $activeButtonAttributes = [
        'class' => 'active',
    ];
    private array $disabledButtonAttributes = [
        'class' => 'disabled',
    ];
    private array $linkAttributes = [
        'class' => 'page-link',
   ];
    private array $activeLinkAttributes = [];
    private array $disabledLinkAttributes = [];
    private array $navAttributes = ['aria-label' => 'Pagination'];
    private array $ulAttributes = ['class' => 'pagination justify-content-center mt-4'];
    private ?string $firstPageLabel = null;
    private array $firstPageAttributes = [];
    private ?string $lastPageLabel = null;
    private array $lastPageAttributes = [
        'class' => 'page-item',
    ];
    private ?string $nextPageLabel = 'Next Page';
    private array $nextPageAttributes = [
        'class' => 'page-item',
    ];
    private string $prevPageLabel = 'Previous';
    private array $prevPageAttributes = [
        'class' => 'page-item',
    ];
    public bool $disableCurrentPageButton = false;
    private string $cssFramework = self::BOOTSTRAP;
    private bool $hideOnSinglePage = false;
    private int $maxButtonCount = 10;
    private bool $registerLinkTags = false;
    private ?array $requestAttributes = null;
    private ?array $requestQueryParams = null;
    private string $pageParam = 'page';
    private CurrentRoute $currentRoute;
    private OffsetPaginator $paginator;
    private UrlGeneratorInterface $urlGenerator;
    private WebView $webView;

    public function __construct(
        CurrentRoute $currentRoute,
        UrlGeneratorInterface $urlGenerator,
        WebView $webView
    ) {
        $this->currentRoute = $currentRoute;
        $this->urlGenerator = $urlGenerator;
        $this->webView = $webView;
    }

    protected function beforeRun(): bool
    {
        if (!isset($this->paginator)) {
            throw new InvalidConfigException('The "paginator" property must be set.');
        }

        if ($this->requestAttributes === null) {
            $this->requestAttributes = $this->currentRoute->getArguments();
        }

        if ($this->requestQueryParams === null) {
            $this->requestQueryParams = [];

            if ($uri = $this->currentRoute->getUri()) {
                parse_str($uri->getQuery(), $this->requestQueryParams);
            }
        }

        return parent::beforeRun();
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
    protected function run(): string
    {
        $this->buildWidget();

        if ($this->registerLinkTags) {
            $this->registerLinkTagsInternal();
        }

        return $this->renderPageButtons();
    }

    /**
     * Name of $_GET page param using for pagination
     *
     * @param string $value
     *
     * @return self
     */
    public function pageParam(string $value): self
    {
        $new = clone $this;
        $new->pageParam = $value;

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
     * @param array $attributes attributes for the active (currently selected) page button.
     *
     * @return self
     */
    public function activeButtonAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->activeButtonAttributes = $attributes;

        return $new;
    }

    /**
     * @param array $attributes HTML attributes for disabled link container
     *
     * @return self
     */
    public function disabledButtonAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->disabledButtonAttributes = $attributes;

        return $new;
    }

    /**
     * @param array $attributes HTML attributes for the "first" page button.
     *
     * @return $this
     */
    public function firstPageAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->firstPageAttributes = $attributes;

        return $new;
    }

    /**
     * @param string|null the text label for the "first" page button.
     *
     * @return $this
     */
    public function firstPageLabel(?string $label): self
    {
        $new = clone $this;
        $new->firstPageLabel = $label;

        return $new;
    }

    /**
     * @param array $attributes HTML attributes for the "last" page button.
     *
     * @return $this
     */
    public function lastPageAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->lastPageAttributes = $attributes;

        return $new;
    }

    /**
     *
     * @param string|null the text label for the "last" page button
     *
     * @return self
     */
    public function lastPageLabel(?string $label): self
    {
        $new = clone $this;
        $new->lastPageLabel = $label;

        return $new;
    }

    /**
     * @param bool $disableCurrentPageButton whether to render current page button as disabled.
     *
     * @return $this
     */
    public function disableCurrentPageButton(bool $disableCurrentPageButton = true): self
    {
        $new = clone $this;
        $new->disableCurrentPageButton = $disableCurrentPageButton;

        return $new;
    }

    public function cssFramework(string $cssFramework): self
    {
        if (!in_array($cssFramework, self::CSS_FRAMEWORKS)) {
            $cssFramework = implode('", "', self::CSS_FRAMEWORKS);
            throw new InvalidConfigException("Invalid CSS framework. Valid values are: \"$cssFramework\".");
        }

        $new = clone $this;
        $new->cssFramework = $cssFramework;

        return $new;
    }

    /**
     * @param bool $hideOnSinglePage Hide widget when only one page exist.
     *
     * @return $this
     */
    public function hideOnSinglePage(bool $hideOnSinglePage = true): self
    {
        $new = clone $this;
        $new->hideOnSinglePage = $hideOnSinglePage;

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
     * @param array $attributes HTML attributes for the active (currently selected) link.
     *
     * @return self
     */
    public function activeLinkAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->activeLinkAttributes = $attributes;

        return $new;
    }

    /**
     * @param array $attributes HTML attributes for the disabled link.
     *
     * @return self
     */
    public function disabledLinkAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->disabledLinkAttributes = $attributes;

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
     * @param array $attributes HTML attributes for the "next" page button.
     *
     * @return $this
     */
    public function nextPageAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->nextPageAttributes = $attributes;

        return $new;
    }

    /**
     * @param string|null $label for the "next" page button
     *
     * @return $this
     */
    public function nextPageLabel(?string $label): self
    {
        $new = clone $this;
        $new->nextPageLabel = $label;

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
     * @param OffsetPaginator $paginator set paginator {@see OffsetPaginator} {@see KeysetPaginator}.
     *
     * @return $this
     */
    public function paginator(OffsetPaginator $paginator): self
    {
        $new = clone $this;
        $new->paginator = $paginator;

        return $new;
    }

    /**
     * @param array $attributes HTML attributes for the "previous" page button.
     *
     * @return $this
     */
    public function prevPageAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->prevPageAttributes = $attributes;

        return $new;
    }

    /**
     * @param string|null $label the text label for the "previous" page button.
     *
     * @return $this
     */
    public function prevPageLabel(?string $label): self
    {
        $new = clone $this;
        $new->prevPageLabel = $label;

        return $new;
    }

    public function requestAttributes(?array $requestAttributes): self
    {
        $new = clone $this;
        $new->requestAttributes = $requestAttributes;

        return $new;
    }

    public function requestQueryParams(?array $requestQueryParams): self
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
    public function registerLinkTags(bool $registerLinkTags = true): self
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
        /** @psalm-var array<string, string> $links */
        $links = $this->createLinks();

        foreach ($links as $rel => $href) {
            $this->webView->registerLinkTag(Link::tag()->url($href)->rel($rel));
        }
    }

    /**
     * Merge default/main attributes for current attributes to active/disabled/etc status
     *
     * @param array $mainAttributes
     * @param array $additionalAttributes
     *
     * @return array
     */
    private static function mergeAttributes(array $mainAttributes, array $additionalAttributes): array
    {
        $class = ArrayHelper::remove($additionalAttributes, 'class');
        $attributes = array_merge($mainAttributes, $additionalAttributes);

        if ($class) {
            Html::addCssClass($attributes, $class);
        }

        return $attributes;
    }

    /**
     * Renders the page buttons.
     *
     * @throws JsonException
     *
     * @return string the rendering result
     */
    private function renderPageButtons(): string
    {
        $currentPage = $this->paginator->getCurrentPage();
        $pageCount = $this->paginator->getTotalPages();

        if ($pageCount < 2 || $this->hideOnSinglePage) {
            return '';
        }

        $renderFirstPageButtonLink = $this->renderFirstPageButtonLink($currentPage);
        $renderPreviousPageButtonLink = $this->renderPreviousPageButtonLink($currentPage);
        $renderPageButtonLinks = $this->renderPageButtonLinks($currentPage);
        $renderNextPageButtonLink = $this->renderNextPageButtonLink($currentPage, $pageCount);
        $renderLastPageButtonLink = $this->renderLastPageButtonLink($currentPage, $pageCount);

        /** @psalm-var non-empty-string */
        $tag = ArrayHelper::remove($this->ulAttributes, 'tag', 'ul');

        $html =
            Html::openTag('nav', $this->navAttributes) . "\n" .
                Html::tag(
                    $tag,
                    "\n" .
                    $renderFirstPageButtonLink . $renderPreviousPageButtonLink .
                    "\n" .
                    implode("\n", $renderPageButtonLinks) .
                    "\n" .
                    $renderNextPageButtonLink . $renderLastPageButtonLink .
                    "\n",
                    $this->ulAttributes
                )->encode(false) . "\n" .
            Html::closeTag('nav');

        if ($this->cssFramework === self::BULMA) {
            $html =
                Html::openTag('nav', $this->navAttributes) . "\n" .
                    trim($renderFirstPageButtonLink) . trim($renderPreviousPageButtonLink) .
                    Html::tag($tag, "\n" . implode("\n", $renderPageButtonLinks), $this->ulAttributes)->encode(false) .
                    trim($renderNextPageButtonLink) . trim($renderLastPageButtonLink) . "\n" .
                Html::closeTag('nav') . "\n";
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

        if ($active && !$disabled) {
            $linkAttributes = self::mergeAttributes($linkAttributes, $this->activeLinkAttributes);
            $buttonsAttributes = self::mergeAttributes($buttonsAttributes, $this->activeButtonAttributes);
        } elseif ($disabled) {
            $linkAttributes = self::mergeAttributes($linkAttributes, $this->disabledLinkAttributes);
            $buttonsAttributes = self::mergeAttributes($buttonsAttributes, $this->disabledButtonAttributes);

            if (!$active) {
                $linkAttributes['aria-disabled'] = 'true';
                $linkAttributes['tabindex'] = '-1';
            }
        }

        $encode = is_numeric($label) ? false : ArrayHelper::remove($buttonsAttributes, 'encode', true);

        return Html::tag(
            $linkWrapTag,
            Html::a(
                $label,
                $this->createUrl($page),
                $linkAttributes
            )->encode($encode)->render()
        )
        ->attributes($buttonsAttributes)
        ->encode(false)
        ->render();
    }

    /**
     * @return array the begin and end pages that need to be displayed.
     */
    private function getPageRange(): array
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
     * Creates the URL suitable for pagination with the specified page number. This method is mainly called by pagers
     * when creating URLs used to perform pagination.
     *
     * @param int $page the zero-based page number that the URL should point to.
     *
     * @return string the created URL.
     *
     * {@see params}
     * {@see forcePageParam}
     */
    private function createUrl(int $page): string
    {
        $params = array_merge($this->requestAttributes, $this->requestQueryParams, [$this->pageParam => $page]);

        if ($name = $this->currentRoute->getName()) {
            return $this->urlGenerator->generate($name, $params);
        }

        if (count($params) > 1) {
            return '?' . http_build_query($params);
        }

        return '';
    }

    private function createLinks(): array
    {
        $currentPage = $this->paginator->getCurrentPage();
        $pageCount = $this->paginator->getTotalPages();

        $links = [self::REL_SELF => $this->createUrl($currentPage)];

        if ($pageCount === 1) {
            $links[self::LINK_FIRST] = $this->createUrl(1);
            $links[self::LINK_LAST] = $this->createUrl($pageCount);
            if ($currentPage > 1) {
                $links[self::LINK_PREV] = $this->createUrl($currentPage);
            }
            if ($currentPage < $pageCount) {
                $links[self::LINK_NEXT] = $this->createUrl($currentPage);
            }
        }

        return $links;
    }

    private function buildWidget(): void
    {
        if ($this->cssFramework === self::BULMA) {
            $this->buildBulma();
        }
    }

    private function buildBulma(): void
    {
        $this->navAttributes['class'] = 'pagination is-centered mt-4';
        $this->ulAttributes['class'] = 'pagination-list justify-content-center mt-4';
        $this->linkAttributes = [];
        $this->buttonsContainerAttributes = ['class' => 'pagination-link'];
        $this->firstPageAttributes = ['class' => 'pagination-previous'];
        $this->lastPageAttributes = ['class' => 'pagination-next'];
        $this->prevPageAttributes = ['class' => 'pagination-previous has-background-link has-text-white'];
        $this->nextPageAttributes = ['class' => 'pagination-next has-background-link has-text-white'];
        $this->activeButtonAttributes = ['class' => 'is-current'];
        $this->disabledButtonAttributes = ['disabled' => true];
    }

    private function renderFirstPageButtonLink(int $currentPage): string
    {
        if ($this->firstPageLabel === null) {
            return '';
        }

        return $this->renderPageButton(
            $this->firstPageLabel,
            1,
            $this->firstPageAttributes,
            $currentPage === 1
        );
    }

    private function renderPreviousPageButtonLink(int $currentPage): string
    {
        if ($this->prevPageLabel === null) {
            return '';
        }

        return $this->renderPageButton(
            $this->prevPageLabel,
            max($currentPage - 1, 1),
            $this->prevPageAttributes,
            $currentPage === 1
        );
    }

    /**
     * @return string[]
     */
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
        if ($this->nextPageLabel === null) {
            return '';
        }

        return $this->renderPageButton(
            $this->nextPageLabel,
            min($pageCount, $currentPage + 1),
            $this->nextPageAttributes,
            $currentPage === $pageCount,
        );
    }

    private function renderLastPageButtonLink(int $currentPage, int $pageCount): string
    {
        if ($this->lastPageLabel === null) {
            return '';
        }

        return $this->renderPageButton(
            $this->lastPageLabel,
            $pageCount,
            $this->lastPageAttributes,
            $currentPage === $pageCount
        );
    }
}
