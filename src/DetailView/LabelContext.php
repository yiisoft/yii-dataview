<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\DetailView;

final class LabelContext
{
    public function __construct(
        public readonly DataField $field,
        public readonly array|object $data,
    ) {
    }
}
