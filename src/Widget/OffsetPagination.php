<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Widget;

use InvalidArgumentException;
use Yiisoft\Html\Tag\Nav;

use function array_filter;
use function array_key_exists;
use function array_merge;
use function max;
use function min;

final class OffsetPagination extends BasePagination
{
    private bool $disabledFirtsPage = false;
    private bool $disabledLastPage = false;
    private bool $disabledPageNavLink = false;
    private string $iconFirtsPage = '';
    private string $iconClassFirtsPage = '';
    private string $iconClassLastPage = '';
    private string $iconLastPage = '';
    private string $labelFirtsPage = '';
    private string $labelLastPage = '';
    private int $maxNavLinkCount = 10;

    /**
     * Return a new instance with disabled first page.
     *
     * @param bool $value
     *
     * @return self
     */
    public function disabledFirtsPage(bool $value): self
    {
        $new = clone $this;
        $new->disabledFirtsPage = $value;

        return $new;
    }

    /**
     * Return a new instance with disabled last page.
     *
     * @param bool $value
     *
     * @return self
     */
    public function disabledLastPage(bool $value): self
    {
        $new = clone $this;
        $new->disabledLastPage = $value;

        return $new;
    }

    /**
     * Return a new instance with disabled page nav link.
     *
     * @param bool $value Disabled page nav link.
     *
     * @return self
     */
    public function disabledPageNavLink(bool $value): self
    {
        $new = clone $this;
        $new->disabledPageNavLink = $value;

        return $new;
    }

    /**
     * Returns a new instance with the icon class for icon attributes `<i>` for link firts page.
     *
     * @param string $value The icon class.
     *
     * @return self
     */
    public function iconClassFirtsPage(string $value): self
    {
        $new = clone $this;
        $new->iconClassFirtsPage = $value;
        $new->labelFirtsPage = '';

        return $new;
    }

    /**
     * Returns a new instance with the icon class for icon attributes `<i>` for link last page.
     *
     * @param string $value The icon class.
     *
     * @return self
     */
    public function iconClassLastPage(string $value): self
    {
        $new = clone $this;
        $new->iconClassLastPage = $value;
        $new->labelLastPage = '';

        return $new;
    }

    /**
     * Return a new instance with icon firts page.
     *
     * @param string $value The icon firts page.
     *
     * @return self
     */
    public function iconFirtsPage(string $value): self
    {
        $new = clone $this;
        $new->iconFirtsPage = $value;
        $new->labelFirtsPage = '';

        return $new;
    }

    /**
     * Return a new instance with icon last page.
     *
     * @param string $value The icon last page.
     *
     * @return self
     */
    public function iconLastPage(string $value): self
    {
        $new = clone $this;
        $new->iconLastPage = $value;
        $new->labelLastPage = '';

        return $new;
    }

    /**
     * Return a new instance with label for first page.
     *
     * @param string $value The label for first page.
     *
     * @return self
     */
    public function labelFirtsPage(string $value = ''): self
    {
        $new = clone $this;
        $new->labelFirtsPage = $value;

        return $new;
    }

    /**
     * Return a new instance with label for last page.
     *
     * @param string $value The label for last page.
     *
     * @return self
     */
    public function labelLastPage(string $value = ''): self
    {
        $new = clone $this;
        $new->labelLastPage = $value;

        return $new;
    }

    /**
     * Return a new instance with max nav link count.
     *
     * @param int $value Max nav link count.
     *
     * @return self
     */
    public function maxNavLinkCount(int $value): self
    {
        $new = clone $this;
        $new->maxNavLinkCount = $value;

        return $new;
    }

    protected function run(): string
    {
        return $this->renderPagination();
    }

    /**
     * @return array The page range of pagination.
     *
     * @psalm-return array<int, int>
     */
    protected function getPageRange(): array
    {
        $beginPage = max(1, $this->getCurrentPage() - (int) ($this->maxNavLinkCount / 2));
        $endPage = 0;
        $paginator = $this->getPaginator()->withNextPageToken('1');

        do {
            $paginator = $paginator->withNextPageToken($paginator->getNextPageToken());
            $endPage++;
        } while ($paginator->isOnLastPage() === false);

        if ($this->getCurrentPage() > $endPage + 1) {
            throw new InvalidArgumentException('Current page must be less than or equal to total pages.');
        }

        return [$beginPage, $endPage + 1];
    }

    private function renderPagination(): string
    {
        $attributes = $this->getAttributes();
        [$beginPage, $endPage] = $this->getPageRange();
        $currentPage = $this->getCurrentPage();
        $items = [];

        if ($this->getHideOnSinglePage() && $beginPage === $endPage) {
            return '';
        }

        $items[] = $this->renderFirstsPageNavLink($currentPage);
        $items[] = $this->renderPreviousPageNavLink($currentPage);
        $items = array_merge($items, $this->renderPageNavLinks($currentPage, $beginPage, $endPage));
        $items[] = $this->renderNextPageNavLink($currentPage, $endPage);
        $items[] = $this->renderLastPageNavLink($currentPage, $endPage);

        if (!array_key_exists('aria-label', $attributes)) {
            $attributes['aria-label'] = 'Pagination';
        }

        return
            Nav::tag()
                ->addAttributes($attributes)
                ->content(
                    PHP_EOL .
                    Menu::widget()
                        ->class($this->getMenuClass())
                        ->items(array_filter($items))
                        ->itemsContainerClass($this->getMenuItemContainerClass())
                        ->linkClass($this->getMenuItemLinkClass()) .
                    PHP_EOL
                )
                ->encode(false)
                ->render();
    }

    private function renderFirstsPageNavLink(int $currentPage): array
    {
        $iconContainerAttributes = $this->getIconContainerAttributes();
        $items = [];

        if (!array_key_exists('aria-hidden', $iconContainerAttributes)) {
            $iconContainerAttributes['aria-hidden'] = 'true';
        }

        if ('' !== $this->labelFirtsPage || '' !== $this->iconFirtsPage || '' !== $this->iconClassFirtsPage) {
            $items = [
                'disabled' => $currentPage === 1 || $this->disabledFirtsPage,
                'icon' => $this->iconFirtsPage,
                'iconAttributes' => $this->getIconAttributes(),
                'iconClass' => $this->iconClassFirtsPage,
                'iconContainerAttributes' => $iconContainerAttributes,
                'label' => $this->labelFirtsPage,
                'link' => $this->createUrl(1),
            ];
        }

        return $items;
    }

    private function renderPreviousPageNavLink(int $currentPage): array
    {
        $iconContainerAttributes = $this->getIconContainerAttributes();

        if (!array_key_exists('aria-hidden', $iconContainerAttributes)) {
            $iconContainerAttributes['aria-hidden'] = 'true';
        }

        return [
            'disabled' => $currentPage === 1 || $this->getDisabledPreviousPage(),
            'icon' => $this->getIconPreviousPage(),
            'iconAttributes' => $this->getIconAttributes(),
            'iconClass' => $this->getIconClassPreviousPage(),
            'iconContainerAttributes' => $iconContainerAttributes,
            'label' => $this->getLabelPreviousPage(),
            'link' => $this->createUrl(max($currentPage - 1, 1)),
        ];
    }

    private function renderPageNavLinks(int $currentPage, int $beginPage, int $endPage): array
    {
        $items = [];

        do {
            $items[] = [
                'active' => $beginPage === $currentPage,
                'disabled' => $this->disabledPageNavLink && $beginPage === $currentPage,
                'label' => $beginPage,
                'link' => $this->createUrl($beginPage),
            ];
        } while (++$beginPage <= $endPage);

        return $items;
    }

    private function renderNextPageNavLink(int $currentPage, int $pageCount): array
    {
        $iconContainerAttributes = $this->getIconContainerAttributes();

        if (!array_key_exists('aria-hidden', $iconContainerAttributes)) {
            $iconContainerAttributes['aria-hidden'] = 'true';
        }

        return [
            'disabled' => $currentPage === $pageCount || $this->getDisabledNextPage(),
            'icon' => $this->getIconNextPage(),
            'iconAttributes' => $this->getIconAttributes(),
            'iconClass' => $this->getIconClassNextPage(),
            'iconContainerAttributes' => $iconContainerAttributes,
            'label' => $this->getLabelNextPage(),
            'link' => $this->createUrl(min($currentPage + 1, $pageCount)),
        ];
    }

    private function renderLastPageNavLink(int $currentPage, int $pageCount): array
    {
        $iconContainerAttributes = $this->getIconContainerAttributes();
        $items = [];

        if (!array_key_exists('aria-hidden', $iconContainerAttributes)) {
            $iconContainerAttributes['aria-hidden'] = 'true';
        }

        if ($this->labelLastPage !== '' || $this->iconLastPage !== '' || $this->iconClassLastPage !== '') {
            $items = [
                'disabled' => $currentPage === $pageCount || $this->disabledLastPage,
                'icon' => $this->iconLastPage,
                'iconAttributes' => $this->getIconAttributes(),
                'iconClass' => $this->iconClassLastPage,
                'iconContainerAttributes' => $iconContainerAttributes,
                'label' => $this->labelLastPage,
                'link' => $this->createUrl($pageCount),
            ];
        }

        return $items;
    }
}
