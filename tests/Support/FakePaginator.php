<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use LogicException;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Paginator\PaginatorInterface;

use function count;

final class FakePaginator implements PaginatorInterface
{
    public function __construct(
        private readonly array $data,
    ) {}

    public function read(): array
    {
        return $this->data;
    }

    public function readOne(): array|object|null
    {
        throw new LogicException('Not implemented.');
    }

    public function isPaginationRequired(): bool
    {
        return false;
    }

    public function getCurrentPageSize(): int
    {
        throw new LogicException('Not implemented.');
    }

    public function withPageSize(int $pageSize): static
    {
        // do nothing
        return $this;
    }

    public function getPageSize(): int
    {
        return count($this->data);
    }

    public function withToken(?PageToken $token): static
    {
        throw new LogicException('Not implemented.');
    }

    public function getToken(): ?PageToken
    {
        throw new LogicException('Not implemented.');
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

    public function withSort(?\Yiisoft\Data\Reader\Sort $sort): static
    {
        throw new LogicException('Not implemented.');
    }

    public function getSort(): ?\Yiisoft\Data\Reader\Sort
    {
        throw new LogicException('Not implemented.');
    }

    public function getFilter(): \Yiisoft\Data\Reader\FilterInterface
    {
        throw new LogicException('Not implemented.');
    }

    public function withFilter(\Yiisoft\Data\Reader\FilterInterface $filter): static
    {
        throw new LogicException('Not implemented.');
    }
}
