<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use Yiisoft\Data\Reader\CountableDataInterface;
use Yiisoft\Data\Reader\LimitableDataInterface;
use Yiisoft\Data\Reader\OffsetableDataInterface;

use function array_slice;
use function count;

/**
 * Test data reader supporting offset, limit and count.
 */
final class SimpleOffsetableCountableLimitableReadable implements
    OffsetableDataInterface,
    CountableDataInterface,
    LimitableDataInterface
{
    private int $offset = 0;
    private ?int $limit = null;

    public function __construct(
        private readonly array $data,
    ) {}

    public function withOffset(int $offset): static
    {
        $new = clone $this;
        $new->offset = $offset;
        return $new;
    }

    public function withLimit(?int $limit): static
    {
        $new = clone $this;
        $new->limit = $limit;
        return $new;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function read(): array
    {
        return array_slice($this->data, $this->offset, $this->limit);
    }

    public function readOne(): array|object|null
    {
        $data = $this->read();
        return count($data) ? reset($data) : null;
    }
}
