<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Widget;

use Stringable;
use InvalidArgumentException;
use JsonException;
use RuntimeException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Link;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
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
final class LinkPager extends AbstractLinkWidget
{
    public const FIRST_PAGE_BUTTON = '{first_page}';
    public const PREV_PAGE_BUTTON = '{prev_page}';
    public const PAGE_LIST = '{page_list}';
    public const PAGES = '{pages}';
    public const NEXT_PAGE_BUTTON = '{next_page}';
    public const LAST_PAGE_BUTTON = '{last_page}';

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
    private bool $hideFirstPageParameter = false;
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
    private array $firstPageAttributes = [
        'class' => 'page-item',
    ];
    private ?string $lastPageLabel = null;
    private array $lastPageAttributes = [
        'class' => 'page-item',
    ];
    private ?string $nextPageLabel = 'Next Page';
    private array $nextPageAttributes = [
        'class' => 'page-item',
    ];
    private ?string $prevPageLabel = 'Previous';
    private array $prevPageAttributes = [
        'class' => 'page-item',
    ];
    public bool $disableCurrentPageButton = false;
    private string $cssFramework = self::BOOTSTRAP;
    private bool $hideOnSinglePage = false;
    private int $maxButtonCount = 10;
    private bool $registerLinkTags = false;
    private string $template = self::PAGE_LIST;
    private string $listTemplate = self::FIRST_PAGE_BUTTON . self::PREV_PAGE_BUTTON . self::PAGES . self::NEXT_PAGE_BUTTON . self::LAST_PAGE_BUTTON;
    private OffsetPaginator $paginator;

    public function __construct(
        CurrentRoute $currentRoute,
        UrlGeneratorInterface $urlGenerator,
        private WebView $webView
    ) {
        parent::__construct($currentRoute, $urlGenerator);
    }

    protected function beforeRun(): bool
    {
        if (!isset($this->paginator)) {
            throw new InvalidConfigException('The "paginator" property must be set.');
        }

        return parent::beforeRun();
    }

    /**
     * Executes the widget.
     *
     * This overrides the parent implementation by displaying the generated page buttons.
     *
     * @throws InvalidConfigException|JsonException
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
     * Set option for widget
     *
     *
     * @throws InvalidArgumentException
     *
     */
    private function setOption(string $name, mixed $value): self
    {
        if (!property_exists($this, $name)) {
            throw new InvalidArgumentException("Attribute {$name} is not defined.");
        }

        $new = clone $this;
        $new->{$name} = $value;

        return $new;
    }

    /**
     * Merge current widget option with new value
     *
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     *
     */
    private function mergeOption(string $name, array $value): self
    {
        if (!property_exists($this, $name)) {
            throw new InvalidArgumentException("Attribute {$name} is not defined.");
        }

        $currentValue = $this->{$name};

        if (!is_array($currentValue)) {
            throw new RuntimeException("Attribute {$name} is not array and it can't be merged.");
        }

        return $this->setOption($name, array_merge($currentValue, $value));
    }

    /**
     * Set template for all widget
     *
     *
     */
    public function template(string $template): self
    {
        $new = clone $this;
        $new->template = $template;

        return $new;
    }

    /**
     * Set template for page list only
     *
     *
     */
    public function listTemplate(string $template): self
    {
        $new = clone $this;
        $new->listTemplate = $template;

        return $new;
    }

    /**
     * @param array $buttonsContainerAttributes HTML attributes which will be applied to all button containers.
     *
     * @return $this
     */
    public function buttonsContainerAttributes(array $buttonsContainerAttributes): self
    {
        return $this->setOption('buttonsContainerAttributes', $buttonsContainerAttributes);
    }

    /**
     * @param array $attributes attributes for the active (currently selected) page button.
     */
    public function activeButtonAttributes(array $attributes): self
    {
        return $this->setOption('activeButtonAttributes', $attributes);
    }

    /**
     * @param string $className the CSS class for the active (currently selected) page button.
     *
     * @return $this
     */
    public function activePageCssClass(string $className): self
    {
        return $this->mergeOption('activeButtonAttributes', ['class' => $className]);
    }

    /**
     * @param array $attributes HTML attributes for disabled link container
     */
    public function disabledButtonAttributes(array $attributes): self
    {
        return $this->setOption('disabledButtonAttributes', $attributes);
    }

    /**
     * @param string $className the CSS class for the disabled page buttons.
     *
     * @return $this
     */
    public function disabledPageCssClass(string $className): self
    {
        return $this->mergeOption('disabledButtonAttributes', ['class' => $className]);
    }

    /**
     * @param string $className the CSS class for the each page button.
     *
     * @return $this
     */
    public function pageCssClass(string $className): self
    {
        return $this->mergeOption('buttonsContainerAttributes', ['class' => $className]);
    }

    /**
     * @param array $attributes HTML attributes for the "first" page button.
     *
     * @return $this
     */
    public function firstPageAttributes(array $attributes): self
    {
        return $this->setOption('firstPageAttributes', $attributes);
    }

    /**
     * @param string $className the CSS class for the "first" page button.
     *
     * @return $this
     */
    public function firstPageCssClass(string $className): self
    {
        return $this->mergeOption('firstPageAttributes', ['class' => $className]);
    }

    /**
     * @param string|null the text label for the "first" page button.
     *
     * @return $this
     */
    public function firstPageLabel(?string $label): self
    {
        return $this->setOption('firstPageLabel', $label);
    }

    /**
     * @param array $attributes HTML attributes for the "last" page button.
     *
     * @return $this
     */
    public function lastPageAttributes(array $attributes): self
    {
        return $this->setOption('lastPageAttributes', $attributes);
    }

    /**
     * @param string $className the CSS class for the "last" page button.
     *
     * @return $this
     */
    public function lastPageCssClass(string $className): self
    {
        return $this->mergeOption('lastPageAttributes', ['class' => $className]);
    }

    /**
     * @param string|null the text label for the "last" page button
     */
    public function lastPageLabel(?string $label): self
    {
        return $this->setOption('lastPageLabel', $label);
    }

    /**
     * @param bool $disableCurrentPageButton whether to render current page button as disabled.
     *
     * @return $this
     */
    public function disableCurrentPageButton(bool $disableCurrentPageButton = true): self
    {
        return $this->setOption('disableCurrentPageButton', $disableCurrentPageButton);
    }

    public function cssFramework(string $cssFramework): self
    {
        if (!in_array($cssFramework, self::CSS_FRAMEWORKS)) {
            $cssFramework = implode('", "', self::CSS_FRAMEWORKS);
            throw new InvalidConfigException("Invalid CSS framework. Valid values are: \"$cssFramework\".");
        }

        return $this->setOption('cssFramework', $cssFramework);
    }

    /**
     * @param bool $hideOnSinglePage Hide widget when only one page exist.
     *
     * @return $this
     */
    public function hideOnSinglePage(bool $hideOnSinglePage = true): self
    {
        return $this->setOption('hideOnSinglePage', $hideOnSinglePage);
    }

    /**
     * Enable/Disable hidding pageParam on first page
     *
     *
     */
    public function hideFirstPageParameter(bool $value = true): self
    {
        $new = clone $this;
        $new->hideFirstPageParameter = $value;

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
        return $this->setOption('linkAttributes', $linkAttributes);
    }

    /**
     * @param array $attributes HTML attributes for the active (currently selected) link.
     */
    public function activeLinkAttributes(array $attributes): self
    {
        return $this->setOption('activeLinkAttributes', $attributes);
    }

    /**
     * @param array $attributes HTML attributes for the disabled link.
     */
    public function disabledLinkAttributes(array $attributes): self
    {
        return $this->setOption('disabledLinkAttributes', $attributes);
    }

    /**
     * @param int $maxButtonCount maximum number of page buttons that can be displayed. Defaults to 10.
     *
     * @return $this
     */
    public function maxButtonCount(int $maxButtonCount): self
    {
        return $this->setOption('maxButtonCount', $maxButtonCount);
    }

    /**
     * @param array $attributes HTML attributes for the "next" page button.
     *
     * @return $this
     */
    public function nextPageAttributes(array $attributes): self
    {
        return $this->setOption('nextPageAttributes', $attributes);
    }

    /**
     * @param string $className the CSS class for the "next" page button.
     *
     * @return $this
     */
    public function nextPageCssClass(string $className): self
    {
        return $this->mergeOption('nextPageAttributes', ['class' => $className]);
    }

    /**
     * @param string|null $label for the "next" page button
     *
     * @return $this
     */
    public function nextPageLabel(?string $label): self
    {
        return $this->setOption('nextPageLabel', $label);
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
        return $this->setOption('navAttributes', $navAttributes);
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
        return $this->setOption('ulAttributes', $ulAttributes);
    }

    /**
     * @param OffsetPaginator $paginator set paginator {@see OffsetPaginator} {@see KeysetPaginator}.
     *
     * @return $this
     */
    public function paginator(OffsetPaginator $paginator): self
    {
        return $this->setOption('paginator', $paginator);
    }

    /**
     * @param array $attributes HTML attributes for the "previous" page button.
     *
     * @return $this
     */
    public function prevPageAttributes(array $attributes): self
    {
        return $this->setOption('prevPageAttributes', $attributes);
    }

    /**
     * @param string $className the CSS class for the "previous" page button.
     *
     * @return $this
     */
    public function prevPageCssClass(string $className): self
    {
        return $this->mergeOption('prevPageAttributes', ['class' => $className]);
    }

    /**
     * @param string|null $label the text label for the "previous" page button.
     *
     * @return $this
     */
    public function prevPageLabel(?string $label): self
    {
        return $this->setOption('prevPageLabel', $label);
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
        return $this->setOption('registerLinkTags', $registerLinkTags);
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
            $this->webView->registerLinkTag(Link::tag()
                ->url($href)
                ->rel($rel));
        }
    }

    /**
     * Merge default/main attributes for current attributes to active/disabled/etc status
     *
     *
     */
    private static function mergeAttributes(array $mainAttributes, array $additionalAttributes): array
    {
        /** @var array<array-key, string>|string|null $class */
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

        $tokens = [
            self::PAGE_LIST => null,
            self::FIRST_PAGE_BUTTON => $this->renderFirstPageButtonLink($currentPage),
            self::PREV_PAGE_BUTTON => $this->renderPreviousPageButtonLink($currentPage),
            self::PAGES => implode('', $this->renderPageButtonLinks($currentPage)),
            self::NEXT_PAGE_BUTTON => $this->renderNextPageButtonLink($currentPage, $pageCount),
            self::LAST_PAGE_BUTTON => $this->renderLastPageButtonLink($currentPage, $pageCount),
        ];

        /** @var string|null $tag */
        $tag = ArrayHelper::remove($this->ulAttributes, 'tag', 'ul');

        if ($tag) {
            $tokens[self::PAGE_LIST] = Html::tag($tag, $this->listTemplate, $this->ulAttributes)
                ->encode(false)
                ->render();
        } else {
            $tokens[self::PAGE_LIST] = $this->listTemplate;
        }

        $search = array_keys($tokens);

        return Html::tag('nav', str_replace($search, $tokens, $this->template), $this->navAttributes)
            ->encode(false)
            ->render();
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
        $linkAttributes = $this->linkAttributes;
        $linkAttributes['data-page'] = $page;

        if ($active) {
            $linkAttributes = self::mergeAttributes($linkAttributes, $this->activeLinkAttributes);
            $buttonsAttributes = self::mergeAttributes($buttonsAttributes, $this->activeButtonAttributes);
        }

        if ($disabled) {
            $linkAttributes = self::mergeAttributes($linkAttributes, $this->disabledLinkAttributes);
            $buttonsAttributes = self::mergeAttributes($buttonsAttributes, $this->disabledButtonAttributes);

            if (!$active) {
                $linkAttributes['aria-disabled'] = 'true';
                $linkAttributes['tabindex'] = '-1';
            }
        }

        /** @var string|null $buttonTag */
        $buttonTag = ArrayHelper::remove($buttonsAttributes, 'tag', 'li');
        /** @var string|null $tag */
        $tag = $active || $disabled ? ArrayHelper::remove($linkAttributes, 'tag') : null;
        /** @var bool $encode */
        $encode = ArrayHelper::remove($buttonsAttributes, 'encode', !is_numeric($label));

        if ($tag) {
            $link = Html::tag($tag, $label, $linkAttributes)
                ->encode($encode)
                ->render();
        } else {
            $link = Html::a($label, $this->createUrl($page), $linkAttributes)
                ->encode($encode)
                ->render();
        }

        if ($buttonTag) {
            return Html::tag($buttonTag, $link, $buttonsAttributes)
                ->encode(false)
                ->render();
        }

        return $link;
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
        $requestArguments = $this->requestArguments ?? [];
        $queryParameters = $this->requestQueryParams ?? [];

        if ($this->hideFirstPageParameter && $page === 1) {
            if ($this->pageArgument) {
                unset($requestArguments[$this->pageParam]);
            } else {
                unset($queryParameters[$this->pageParam]);
            }
        } elseif ($this->pageArgument) {
            $requestArguments[$this->pageParam] = $page;
        } else {
            $queryParameters[$this->pageParam] = $page;
        }

        if ($name = $this->currentRoute->getName()) {
            /** @var array<string, scalar|Stringable|null> $requestArguments */
            return $this->urlGenerator->generate($name, $requestArguments, $queryParameters);
        }

        return $queryParameters ? '?' . http_build_query($queryParameters) : '';
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
        $this->template = self::FIRST_PAGE_BUTTON . self::PREV_PAGE_BUTTON . self::PAGE_LIST . self::NEXT_PAGE_BUTTON . self::LAST_PAGE_BUTTON;
        $this->listTemplate = self::PAGES;
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
