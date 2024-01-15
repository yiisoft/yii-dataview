<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use InvalidArgumentException;
use Stringable;
use Yiisoft\Data\Paginator\OffsetPaginator;

use function max;
use function min;

final class OffsetPagination extends BasePagination
{
    private OffsetPaginator|null $paginator = null;

    private string|Stringable|null $labelPrevious = 'Previous';
    private string|Stringable|null $labelNext = 'Next';
    private string|Stringable|null $labelFirst = 'First';
    private string|Stringable|null $labelLast = 'Last';

    private int $maxNavLinkCount = 10;

    public function paginator(OffsetPaginator $paginator): self
    {
        $new = clone $this;
        $new->paginator = $paginator;
        return $new;
    }

    /**
     * Return a new instance with max nav link count.
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
                url: $this->createUrl(1),
                isCurrent: false,
                isDisabled: $currentPage === 1,
            );
        }

        if ($this->labelPrevious !== null) {
            $items[] = new PaginationItem(
                label: $this->labelPrevious,
                url: $this->createUrl(max($currentPage - 1, 1)),
                isCurrent: false,
                isDisabled: $currentPage === 1,
            );
        }

        $page = $beginPage;
        do {
            $items[] = new PaginationItem(
                label: (string)$page,
                url: $this->createUrl($page),
                isCurrent: $page === $currentPage,
                isDisabled: false,
            );
        } while (++$page <= $endPage);

        if ($this->labelNext !== null) {
            $items[] = new PaginationItem(
                label: $this->labelNext,
                url: $this->createUrl(min($currentPage + 1, $totalPages)),
                isCurrent: false,
                isDisabled: $currentPage === $totalPages,
            );
        }

        if ($this->labelLast !== null) {
            $items[] = new PaginationItem(
                label: $this->labelLast,
                url: $this->createUrl($totalPages),
                isCurrent: false,
                isDisabled: $currentPage === $totalPages,
            );
        }

        return $items;
    }

    protected function getPaginator(): OffsetPaginator
    {
        return $this->paginator === null
            ? throw new Exception\PaginatorNotSetException()
            : $this->paginator;
    }
}
