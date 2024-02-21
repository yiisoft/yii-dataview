<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Widget;

final class Context
{
    public function __construct(
        public readonly string $property,
        public readonly ?string $value,
        public readonly string $formId,
    ) {
    }
}
