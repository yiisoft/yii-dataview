<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Yiisoft\Yii\DataView\Column\ColumnInterface;

final class DataContext
{
    public function __construct(
        private ColumnInterface $column,
        private array|object $data,
        private mixed $key,
        private int $index,
    ) {
    }

    public function getColumn(): ColumnInterface
    {
        return $this->column;
    }

    public function getData(): array|object
    {
        return $this->data;
    }

    public function getKey(): mixed
    {
        return $this->key;
    }

    public function getIndex(): int
    {
        return $this->index;
    }
}
