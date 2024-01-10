<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

final class PageContext
{
    public function __construct(
        public readonly int $page,
        public readonly int $pageSize,
        public readonly string $pageParameterName,
        public readonly string $pageSizeParameterName,
        public readonly int $pageParameterType,
        public readonly int $pageSizeParameterType,
        public readonly array $queryParameters,
        public readonly int $defaultPageSize,
    ) {
    }
}
