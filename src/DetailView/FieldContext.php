<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\DetailView;

final class FieldContext
{
    public function __construct(
        public readonly DataField $field,
        public readonly array|object $data,
        public readonly string $value,
        public readonly string $label,
    ) {
    }
}
