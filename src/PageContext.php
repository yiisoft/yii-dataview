<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

final class PageContext
{
    public function __construct(
        public readonly int|string $page,
        public readonly int $pageSize,
        public readonly bool $isPreviousPage,
        public readonly bool $isFirstPage,
        public readonly string $pageParameterName,
        public readonly string $previousPageParameterName,
        public readonly string $pageSizeParameterName,
        public readonly int $pageParameterType,
        public readonly int $previousPageParameterType,
        public readonly int $pageSizeParameterType,
        public readonly array $queryParameters,
        public readonly int $defaultPageSize,
    ) {
    }
}
