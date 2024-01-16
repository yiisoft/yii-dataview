<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Stringable;
use Yiisoft\Data\Paginator\KeysetPaginator;

final class KeysetPagination extends BasePagination
{
    private KeysetPaginator|null $paginator = null;

    private string|Stringable $labelPrevious = 'Previous';
    private string|Stringable $labelNext = 'Next';

    public function paginator(KeysetPaginator $paginator): self
    {
        $new = clone $this;
        $new->paginator = $paginator;
        return $new;
    }

    protected function getItems(): array
    {
        $paginator = $this->getPaginator();
        $previousToken = $paginator->getPreviousPageToken();
        $nextToken = $paginator->getNextPageToken();

        return [
            new PaginationItem(
                label: $this->labelPrevious,
                url: $previousToken === null ? null : $this->createUrl($previousToken, true),
                isCurrent: false,
                isDisabled: $previousToken === null,
            ),
            new PaginationItem(
                label: $this->labelNext,
                url: $nextToken === null ? null : $this->createUrl($nextToken),
                isCurrent: false,
                isDisabled: $nextToken === null,
            ),
        ];
    }

    protected function getPaginator(): KeysetPaginator
    {
        return $this->paginator ?? throw new Exception\PaginatorNotSetException();
    }

    protected function isFirstPage(int|string $page, bool $isPrevious): bool
    {
        return false;
    }
}
