<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

final class BodyRowContext
{
    /**
     * @param array|object $data Row data.
     * @param int|string $key
     * @param int $index
     */
    public function __construct(
        public readonly array|object $data,
        public readonly int|string $key,
        public readonly int $index,
    ) {
    }
}
