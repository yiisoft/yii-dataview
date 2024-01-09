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
        public readonly int $pageParameterPlace,
        public readonly int $pageSizeParameterPlace,
    ) {
    }
}
