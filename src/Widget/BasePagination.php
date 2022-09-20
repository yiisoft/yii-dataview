<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Widget;

use Forge\Html\Widgets\Components\Nav;
use Forge\Html\Widgets\Components\NavBar;
use InvalidArgumentException;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\DataView\Exception\PaginatorNotSetException;

use function array_filter;
use function array_key_exists;
use function array_merge;
use function http_build_query;
use function max;
use function min;

abstract class BasePagination extends Widget
{
    private array $attributes = [];
    private int $currentPage = 0;
    private bool $disabledNextPage = false;
    private bool $disabledPreviousPage = false;
    private bool $hideOnSinglePage = true;
    private array $iconAttributes = [];
    private string $iconClassNextPage = '';
    private string $iconClassPreviousPage = '';
    private array $iconContainerAttributes = [];
    private string $iconNextPage = '';
    private string $iconPreviousPage = '';
    private string $labelNextPage = 'Next Page';
    private string $labelPreviousPage = 'Previous';
    private string $menuClass = 'pagination';
    private string $menuItemContainerClass = 'page-item';
    private string $menuItemLinkClass = 'page-link';
    private array $pageConfig = [];
    private string $pageName = 'page';
    private string $pageSizeName = 'pagesize';
    private PaginatorInterface|null $paginator = null;
    private UrlGeneratorInterface|null $urlGenerator = null;
    private ?array $urlArguments = [];
    private bool $urlEnabledArguments = true;
    private array $urlQueryParameters = [];
    private string|null $urlName = null;

    /**
     * Returns a new instance with the HTML attributes. The following special options are recognized.
     *
     * @param array $values Attribute values indexed by attribute names.
     *
     * @return static
     */
    public function attributes(array $values): static
    {
        $new = clone $this;
        $new->attributes = $values;

        return $new;
    }

    /**
     * Return a new instance with current page of pagination.
     *
     * @param int $value Current page.
     *
     * @return self
     */
    public function currentPage(int $value): self
    {
        $new = clone $this;
        $new->currentPage = $value;

        return $new;
    }

    /**
     * Return a new instance with disabled next page.
     *
     * @param bool $value
     *
     * @return static
     */
    public function disabledNextPage(bool $value): static
    {
        $new = clone $this;
        $new->disabledNextPage = $value;

        return $new;
    }

    /**
     * Return a new instance with disabled previous page.
     *
     * @param bool $value
     *
     * @return static
     */
    public function disabledPreviousPage(bool $value): static
    {
        $new = clone $this;
        $new->disabledPreviousPage = $value;

        return $new;
    }

    /**
     * Return a new instance with hide on single page.
     *
     * @param bool $value The value indicating whether to hide the widget when there is only one page.
     *
     * @return static
     */
    public function hideOnSinglePage(bool $value): static
    {
        $new = clone $this;
        $new->hideOnSinglePage = $value;

        return $new;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getDisabledNextPage(): bool
    {
        return $this->disabledNextPage;
    }

    public function getDisabledPreviousPage(): bool
    {
        return $this->disabledPreviousPage;
    }

    public function getHideOnSinglePage(): bool
    {
        return $this->hideOnSinglePage;
    }

    public function getIconAttributes(): array
    {
        return $this->iconAttributes;
    }

    public function getIconClassNextPage(): string
    {
        return $this->iconClassNextPage;
    }

    public function getIconClassPreviousPage(): string
    {
        return $this->iconClassPreviousPage;
    }

    public function getIconContainerAttributes(): array
    {
        return $this->iconContainerAttributes;
    }

    public function getIconNextPage(): string
    {
        return $this->iconNextPage;
    }

    public function getIconPreviousPage(): string
    {
        return $this->iconPreviousPage;
    }

    public function getLabelPreviousPage(): string
    {
        return $this->labelPreviousPage;
    }

    public function getLabelNextPage(): string
    {
        return $this->labelNextPage;
    }

    public function getMenuClass(): string
    {
        return $this->menuClass;
    }

    public function getMenuItemContainerClass(): string
    {
        return $this->menuItemContainerClass;
    }

    public function getMenuItemLinkClass(): string
    {
        return $this->menuItemLinkClass;
    }

    public function getPaginator(): PaginatorInterface
    {
        if ($this->paginator === null) {
            throw new PaginatorNotSetException();
        }

        return $this->paginator;
    }

    /**
     * Returns a new instance with the HTML attributes for icon attributes `<i>`.
     *
     * @param array $values Attribute values indexed by attribute names.
     *
     * @return static
     */
    public function iconAttributes(array $values): static
    {
        $new = clone $this;
        $new->iconAttributes = $values;

        return $new;
    }

    /**
     * Returns a new instance with the icon class for icon attributes `<i>` for link next page.
     *
     * @param string $value The icon class.
     *
     * @return static
     */
    public function iconClassNextPage(string $value): static
    {
        $new = clone $this;
        $new->iconClassNextPage = $value;
        $new->labelNextPage = '';

        return $new;
    }

    /**
     * Returns a new instance with the icon class for icon attributes `<i>` for link previous page.
     *
     * @param string $value The icon class.
     *
     * @return static
     */
    public function iconClassPreviousPage(string $value): static
    {
        $new = clone $this;
        $new->iconClassPreviousPage = $value;
        $new->labelPreviousPage = '';

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for icon container attributes `<span>`.
     *
     * @param array $values Attribute values indexed by attribute names.
     *
     * @return static
     */
    public function iconContainerAttributes(array $values): static
    {
        $new = clone $this;
        $new->iconContainerAttributes = $values;

        return $new;
    }

    /**
     * Return a new instance with icon next page.
     *
     * @param string $value The icon next page.
     *
     * @return static
     */
    public function iconNextPage(string $value): static
    {
        $new = clone $this;
        $new->iconNextPage = $value;
        $new->labelNextPage = '';

        return $new;
    }

    /**
     * Return a new instance with icon previous page.
     *
     * @param string $value The icon previous page.
     *
     * @return static
     */
    public function iconPreviousPage(string $value): static
    {
        $new = clone $this;
        $new->iconPreviousPage = $value;
        $new->labelPreviousPage('');

        return $new;
    }

    /**
     * Return a new instance with label for next page.
     *
     * @param string $value The label for next page.
     *
     * @return static
     */
    public function labelNextPage(string $value = ''): static
    {
        $new = clone $this;
        $new->labelNextPage = $value;

        return $new;
    }

    /**
     * Return a new instance with label for previous page.
     *
     * @param string $value The label for previous page.
     *
     * @return static
     */
    public function labelPreviousPage(string $value = ''): static
    {
        $new = clone $this;
        $new->labelPreviousPage = $value;

        return $new;
    }

    /**
     * Return a new instance with class css for menu tag `<ul>`.
     *
     * @param string $value The class css for menu tag `<ul>`.
     *
     * @return static
     */
    public function menuClass(string $value): static
    {
        $new = clone $this;
        $new->menuClass = $value;

        return $new;
    }

    /**
     * Return a new instance with class css for menu item tag `<li>`.
     *
     * @param string $value The class css for menu item tag `<li>`.
     *
     * @return static
     */
    public function menuItemContainerClass(string $value): static
    {
        $new = clone $this;
        $new->menuItemContainerClass = $value;

        return $new;
    }

    /**
     * Return a new instance with class css for menu link tag `<a>`.
     *
     * @param string $value The class css for menu link tag `<a>`.
     *
     * @return static
     */
    public function menuLinkClass(string $value): static
    {
        $new = clone $this;
        $new->menuLinkClass = $value;

        return $new;
    }

    /**
     * Return a new instance with page config for arguments or query parameters in url.
     *
     * @param array $value The page config for arguments or query parameters in url.
     *
     * @return static
     */
    public function pageConfig(array $value): static
    {
        $new = clone $this;
        $new->pageConfig = $value;

        return $new;
    }

    /**
     * Return a new instance with name of argument or query parameter for page.
     *
     * @param string $value The name of argument or query parameter for page.
     *
     * @return static
     */
    public function pageName(string $value): static
    {
        $new = clone $this;
        $new->pageName = $value;

        return $new;
    }

    /**
     * Return a new instance with name of argument or query parameter for page size.
     *
     * @param string $value The name of argument or query parameter for page size.
     *
     * @return static
     */
    public function pageSizeName(string $value): static
    {
        $new = clone $this;
        $new->pageSizeName = $value;

        return $new;
    }

    /**
     * Returns a new instance with the paginator interface of the grid view, detail view, or list view.
     *
     * @param PaginatorInterface $value The paginator interface of the grid view, detail view, or list view.
     *
     * @return static
     */
    public function paginator(PaginatorInterface $value): static
    {
        $new = clone $this;
        $new->paginator = $value;

        return $new;
    }

    /**
     * Return a new instance with arguments of the route.
     *
     * @param array $value Arguments of the route.
     *
     * @return static
     */
    public function urlArguments(array $value): static
    {
        $new = clone $this;
        $new->urlArguments = $value;

        return $new;
    }

    /**
     * Return a new instance with url generator interface for pagination.
     *
     * @param UrlGeneratorInterface $value The url generator interface for pagination.
     *
     * @return static
     */
    public function urlGenerator(UrlGeneratorInterface $value): static
    {
        $new = clone $this;
        $new->urlGenerator = $value;

        return $new;
    }

    /**
     * Returns a new instance with the name of the route.
     *
     * @param string $value The name of the route.
     *
     * @return static
     */
    public function urlName(string $value): static
    {
        $new = clone $this;
        $new->urlName = $value;

        return $new;
    }

    /**
     * Return a new instance with query parameters of the route.
     *
     * @param array $value The query parameters of the route.
     *
     * @return static
     */
    public function urlQueryParameters(array $value): static
    {
        $new = clone $this;
        $new->urlQueryParameters = $value;

        return $new;
    }

    /**
     * Creates the URL suitable for pagination with the specified page number. This method is mainly called by pagers
     * when creating URLs used to perform pagination.
     *
     * @param int $page the zero-based page number that the URL should point to.
     *
     * @return string the created URL.
     */
    protected function createUrl(int $page): string
    {
        if ($this->urlGenerator === null) {
            throw new InvalidArgumentException('UrlGenerator must be configured.');
        }

        $pageConfig = $this->pageConfig;
        $urlQueryParameters = $this->urlQueryParameters;

        if ($pageConfig === []) {
            $pageConfig = [
                $this->pageName => (string) $page,
                $this->pageSizeName => (string) $this->getPaginator()->getPageSize(),
            ];
        }

        if ($this->urlArguments !== null) {
            /** @psalm-var array<string,string> */
            $urlArguments = array_merge($this->urlArguments, $pageConfig);
        } else {
            $urlArguments = [];
            $urlQueryParameters = array_merge($this->urlQueryParameters, $pageConfig);
        }

        return match ($this->urlName !== null) {
            true => $this->urlGenerator->generate($this->urlName, $urlArguments, $urlQueryParameters),
            false => $urlQueryParameters ? '?' . http_build_query($urlQueryParameters) : '',
        };
    }
}
