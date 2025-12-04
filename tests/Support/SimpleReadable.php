<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use Yiisoft\Data\Reader\ReadableDataInterface;

use function count;

final class SimpleReadable implements ReadableDataInterface
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
        return count($this->data) ? reset($this->data) : null;
    }
}
