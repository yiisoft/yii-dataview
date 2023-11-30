<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Stringable;

final class UrlConfig
{
    public const ARGUMENTS = true;
    public const QUERY_PARAMETERS = false;

    /**
     * @psalm-param array<string,scalar|Stringable|null> $arguments
     */
    public function __construct(
        public readonly ?string $baseRouteName = null,
        public readonly array $arguments = [],
        public readonly array $queryParameters = [],
        public readonly ?bool $primaryKeyPlace = null,
    ) {
    }
}
