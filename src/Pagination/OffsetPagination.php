<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use InvalidArgumentException;
use Stringable;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Yii\DataView\Exception;

use function max;
use function min;

final class OffsetPagination extends BasePagination
{
    private OffsetPaginator|null $paginator = null;

    private string|Stringable|null $labelPrevious = '⟨';
    private string|Stringable|null $labelNext = '⟩';
    private string|Stringable|null $labelFirst = '⟪';
    private string|Stringable|null $labelLast = '⟫';

    private int $maxNavLinkCount = 10;

    public function withPaginator(PaginatorInterface $paginator): static
    {
        if (!$paginator instanceof OffsetPaginator) {
            throw new PaginatorNotSupportedException($paginator);
        }

        $new = clone $this;
        $new->paginator = $paginator;
        return $new;
    }

    public function labelPrevious(string|Stringable|null $label): self
    {
        $new = clone $this;
        $new->labelPrevious = $label;
        return $new;
    }

    public function labelNext(string|Stringable|null $label): self
    {
        $new = clone $this;
        $new->labelNext = $label;
        return $new;
    }

    public function labelFirst(string|Stringable|null $label): self
    {
        $new = clone $this;
        $new->labelFirst = $label;
        return $new;
    }

    public function labelLast(string|Stringable|null $label): self
    {
        $new = clone $this;
        $new->labelLast = $label;
        return $new;
    }

    /**
     * Return a new instance with a max nav link count.
     *
     * @param int $value Max nav link count.
     */
    public function maxNavLinkCount(int $value): self
    {
        $new = clone $this;
        $new->maxNavLinkCount = $value;

        return $new;
    }

    /**
     * @psalm-return array<int, int>
     */
    protected function getPageRange(int $currentPage, int $totalPages): array
    {
        $beginPage = max(1, $currentPage - (int) ($this->maxNavLinkCount / 2));

        if (($endPage = $beginPage + $this->maxNavLinkCount - 1) >= $totalPages) {
            $endPage = $totalPages;
            $beginPage = max(1, $endPage - $this->maxNavLinkCount + 1);
        }

        if ($totalPages !== 0 && $currentPage > $totalPages) {
            throw new InvalidArgumentException('Current page must be less than or equal to total pages.');
        }

        return [$beginPage, $endPage];
    }

    protected function getItems(): array
    {
        $paginator = $this->getPaginator();
        $currentPage = $paginator->getCurrentPage();
        $totalPages = $paginator->getTotalPages();
        [$beginPage, $endPage] = $this->getPageRange($currentPage, $totalPages);

        $items = [];

        if ($this->labelFirst !== null) {
            $items[] = new PaginationItem(
                label: $this->labelFirst,
                url: $this->createUrl(PageToken::next('1')),
                isCurrent: false,
                isDisabled: $currentPage === 1,
            );
        }

        if ($this->labelPrevious !== null) {
            $items[] = new PaginationItem(
                label: $this->labelPrevious,
                url: $this->createUrl(PageToken::next((string) max($currentPage - 1, 1))),
                isCurrent: false,
                isDisabled: $currentPage === 1,
            );
        }

        $page = $beginPage;
        do {
            $items[] = new PaginationItem(
                label: (string) $page,
                url: $this->createUrl(PageToken::next((string) $page)),
                isCurrent: $page === $currentPage,
                isDisabled: false,
            );
        } while (++$page <= $endPage);

        if ($this->labelNext !== null) {
            $items[] = new PaginationItem(
                label: $this->labelNext,
                url: $this->createUrl(PageToken::next((string) min($currentPage + 1, $totalPages))),
                isCurrent: false,
                isDisabled: $currentPage === $totalPages,
            );
        }

        if ($this->labelLast !== null) {
            $items[] = new PaginationItem(
                label: $this->labelLast,
                url: $this->createUrl(PageToken::next((string) $totalPages)),
                isCurrent: false,
                isDisabled: $currentPage === $totalPages,
            );
        }

        return $items;
    }

    /**
     * @param PageToken $pageToken Token for the page.
     * @return string Created URL.
     */
    private function createUrl(PageToken $pageToken): string
    {
        $context = $this->getContext();
        return $pageToken->value === '1'
            ? $context->defaultUrl
            : $context->createUrl($pageToken);
    }

    private function getPaginator(): OffsetPaginator
    {
        return $this->paginator ?? throw new PaginatorNotSetException();
    }
}
