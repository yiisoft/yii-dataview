<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use Stringable;

/**
 * @internal
 */
final class PaginationItem
{
    public function __construct(
        public readonly string|Stringable $label,
        public readonly string|null $url,
        public readonly bool $isCurrent,
        public readonly bool $isDisabled,
    ) {
    }
}
