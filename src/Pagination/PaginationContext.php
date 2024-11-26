<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

final class PaginationContext
{
    public const URL_PLACEHOLDER = 'YII-DATAVIEW-PAGE-PLACEHOLDER';

    /**
     * @internal
     *
     * @param array<string, string> $overrideOrderFields
     */
    public function __construct(
        public readonly array $overrideOrderFields,
        public readonly string $nextUrlPattern,
        public readonly string $previousUrlPattern,
        public readonly string $defaultUrl,
    ) {
    }
}
