<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

final class QueryProperty
{
    public function __construct(
        public readonly string $property,
        public readonly string $field,
    ) {
    }

    public function hasEqualField(): bool
    {
        return $this->property === $this->field;
    }
}
