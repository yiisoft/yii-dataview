<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Yiisoft\Yii\DataView\Column\ColumnInterface;

final class DataContext
{
    public function __construct(
        public readonly ColumnInterface $column,
        public readonly array|object $data,
        public readonly mixed $key,
        public readonly int $index,
    ) {
    }
}
