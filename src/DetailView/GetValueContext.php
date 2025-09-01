<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\DetailView;

final class GetValueContext
{
    public function __construct(
        public readonly DataField $field,
        public readonly array|object $data,
    ) {
    }
}
