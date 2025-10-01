<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use LogicException;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\FilterableDataInterface;
use Yiisoft\Data\Reader\FilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\LimitableDataInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Data\Reader\SortableDataInterface;

/**
 * Test data reader that implements interfaces needed for {@see KeysetPaginator} auto-creation.
 */
final class FilterableSortableLimitableDataReader implements
    FilterableDataInterface,
    SortableDataInterface,
    LimitableDataInterface
{
    private ?Sort $sort = null;
    private ?int $limit = null;

    public function __construct(
        private readonly array $data,
    ) {
    }

    public function withFilter(FilterInterface $filter): static
    {
        throw new LogicException('Not supported.');
    }

    public function withSort(?Sort $sort): static
    {
        $new = clone $this;
        $new->sort = $sort;
        return $new;
    }

    public function withLimit(?int $limit): static
    {
        $new = clone $this;
        $new->limit = $limit;
        return $new;
    }

    public function withAddedFilterHandlers(FilterHandlerInterface ...$filterHandlers): static
    {
        throw new LogicException('Not supported.');
    }

    public function getFilter(): FilterInterface
    {
        return new All();
    }

    public function getSort(): ?Sort
    {
        return $this->sort;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function read(): array
    {
        if ($this->limit === null) {
            return $this->data;
        }

        return array_slice($this->data, 0, $this->limit);
    }

    public function readOne(): array|object|null
    {
        return $this->data[0] ?? null;
    }
}
