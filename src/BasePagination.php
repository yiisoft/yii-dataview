<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Widget\Widget;

/**
 * @psalm-type UrlCreator = callable(PageContext):string
 */
abstract class BasePagination extends Widget
{
    /**
     * @psalm-var UrlCreator|null
     */
    private $urlCreator;
    private string $pageParameterName = 'page';
    private string $pageSizeParameterName = 'pagesize';

    /**
     * @psalm-var UrlParameterType::*
     */
    private int $pageParameterType = UrlParameterType::QUERY;

    /**
     * @psalm-var UrlParameterType::*
     */
    private int $pageSizeParameterType = UrlParameterType::QUERY;

    private int $defaultPageSize = PaginatorInterface::DEFAULT_PAGE_SIZE;

    private array $queryParameters = [];

    private array $attributes = [];
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

    /**
     * @psalm-param UrlCreator|null $urlCreator
     */
    public function urlCreator(?callable $urlCreator): static
    {
        $new = clone $this;
        $new->urlCreator = $urlCreator;
        return $new;
    }

    final public function defaultPageSize(int $size): static
    {
        $new = clone $this;
        $new->defaultPageSize = $size;
        return $new;
    }

    /**
     * Return a new instance with query parameters of the route.
     *
     * @param array $value The query parameters of the route.
     */
    public function queryParameters(array $value): static
    {
        $new = clone $this;
        $new->queryParameters = $value;
        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes. The following special options are recognized.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function attributes(array $values): static
    {
        $new = clone $this;
        $new->attributes = $values;

        return $new;
    }

    /**
     * Return a new instance with disabled next page.
     *
     * @param bool $value Whether to disable next page.
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
     * @param bool $value Whether to disable previous page.
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
     */
    public function hideOnSinglePage(bool $value): static
    {
        $new = clone $this;
        $new->hideOnSinglePage = $value;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for icon attributes `<i>`.
     *
     * @param array $values Attribute values indexed by attribute names.
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
     */
    public function iconPreviousPage(string $value): static
    {
        $new = clone $this;
        $new->iconPreviousPage = $value;
        $new->labelPreviousPage = '';

        return $new;
    }

    /**
     * Return a new instance with label for next page.
     *
     * @param string $value The label for next page.
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
     */
    public function menuItemContainerClass(string $value): static
    {
        $new = clone $this;
        $new->menuItemContainerClass = $value;

        return $new;
    }

    /**
     * Return a new instance with name of argument or query parameter for page.
     *
     * @param string $name The name of argument or query parameter for page.
     */
    public function pageParameterName(string $name): static
    {
        $new = clone $this;
        $new->pageParameterName = $name;
        return $new;
    }

    /**
     * Return a new instance with name of argument or query parameter for page size.
     *
     * @param string $name The name of argument or query parameter for page size.
     */
    public function pageSizeParameterName(string $name): static
    {
        $new = clone $this;
        $new->pageSizeParameterName = $name;

        return $new;
    }

    /**
     * Creates the URL suitable for pagination with the specified page number. This method is mainly called by pagers
     * when creating URLs used to perform pagination.
     *
     * @param int $page the zero-based page number that the URL should point to.
     */
    protected function createUrl(int $page): string
    {
        if ($this->urlCreator === null) {
            return '#' . $page;
        }

        return call_user_func(
            $this->urlCreator,
            new PageContext(
                $page,
                $this->getPaginator()->getPageSize(),
                $this->pageParameterName,
                $this->pageSizeParameterName,
                $this->pageParameterType,
                $this->pageSizeParameterType,
                $this->queryParameters,
                $this->defaultPageSize,
            )
        );
    }

    protected function getAttributes(): array
    {
        return $this->attributes;
    }

    protected function getDisabledNextPage(): bool
    {
        return $this->disabledNextPage;
    }

    protected function getDisabledPreviousPage(): bool
    {
        return $this->disabledPreviousPage;
    }

    protected function getHideOnSinglePage(): bool
    {
        return $this->hideOnSinglePage;
    }

    protected function getIconAttributes(): array
    {
        return $this->iconAttributes;
    }

    protected function getIconClassNextPage(): string
    {
        return $this->iconClassNextPage;
    }

    protected function getIconClassPreviousPage(): string
    {
        return $this->iconClassPreviousPage;
    }

    protected function getIconContainerAttributes(): array
    {
        return $this->iconContainerAttributes;
    }

    protected function getIconNextPage(): string
    {
        return $this->iconNextPage;
    }

    protected function getIconPreviousPage(): string
    {
        return $this->iconPreviousPage;
    }

    protected function getLabelPreviousPage(): string
    {
        return $this->labelPreviousPage;
    }

    protected function getLabelNextPage(): string
    {
        return $this->labelNextPage;
    }

    protected function getMenuClass(): string
    {
        return $this->menuClass;
    }

    protected function getMenuItemContainerClass(): string
    {
        return $this->menuItemContainerClass;
    }

    protected function getMenuItemLinkClass(): string
    {
        return $this->menuItemLinkClass;
    }

    abstract protected function getPaginator(): PaginatorInterface;
}
