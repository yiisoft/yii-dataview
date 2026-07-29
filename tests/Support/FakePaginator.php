<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use LogicException;
use Yiisoft\Data\Paginator\PageNotFoundException;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\Sort;

use function count;

final class FakePaginator implements PaginatorInterface
{
    private ?PageToken $token = null;
    private int $pageSize;

    public function __construct(
        private readonly array $data,
        private readonly bool $paginationRequired = false,
        private readonly bool $throwOnToken = false,
        private readonly bool $throwOnRead = false,
        private readonly bool $throwOnReadWithToken = false,
    ) {
        $this->pageSize = count($data);
    }

    public function read(): array
    {
        if ($this->throwOnRead || ($this->throwOnReadWithToken && $this->token !== null)) {
            throw new PageNotFoundException((int) ($this->token?->value ?? 999));
        }

        return $this->data;
    }

    public function readOne(): array|object|null
    {
        throw new LogicException('Not implemented.');
    }

    public function isPaginationRequired(): bool
    {
        return $this->paginationRequired;
    }

    public function getCurrentPageSize(): int
    {
        throw new LogicException('Not implemented.');
    }

    public function withPageSize(int $pageSize): static
    {
        $new = clone $this;
        $new->pageSize = $pageSize;
        return $new;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function withToken(?PageToken $token): static
    {
        if ($this->throwOnToken && $token !== null) {
            throw new PageNotFoundException((int) $token->value);
        }

        $new = clone $this;
        $new->token = $token;
        return $new;
    }

    public function getToken(): ?PageToken
    {
        return $this->token;
    }

    public function getNextToken(): ?PageToken
    {
        throw new LogicException('Not implemented.');
    }

    public function getPreviousToken(): ?PageToken
    {
        throw new LogicException('Not implemented.');
    }

    public function nextPage(): static
    {
        throw new LogicException('Not implemented.');
    }

    public function previousPage(): static
    {
        throw new LogicException('Not implemented.');
    }

    public function withNextPageToken(?string $value): static
    {
        throw new LogicException('Not implemented.');
    }

    public function withPreviousPageToken(?string $value): static
    {
        throw new LogicException('Not implemented.');
    }

    public function isOnFirstPage(): bool
    {
        return true;
    }

    public function isOnLastPage(): bool
    {
        throw new LogicException('Not implemented.');
    }

    public function isRequired(): bool
    {
        throw new LogicException('Not implemented.');
    }

    public function isSortable(): bool
    {
        return false;
    }

    public function isFilterable(): bool
    {
        throw new LogicException('Not implemented.');
    }

    public function withSort(?Sort $sort): static
    {
        throw new LogicException('Not implemented.');
    }

    public function getSort(): ?Sort
    {
        throw new LogicException('Not implemented.');
    }

    public function getFilter(): FilterInterface
    {
        throw new LogicException('Not implemented.');
    }

    public function withFilter(FilterInterface $filter): static
    {
        throw new LogicException('Not implemented.');
    }
}
