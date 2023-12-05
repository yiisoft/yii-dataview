<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Stringable;

final class UrlConfig
{
    public const ARGUMENTS = true;
    public const QUERY_PARAMETERS = false;

    /**
     * @param string|null $primaryKey The primary key of the data to be used to generate a URL.
     *
     * @psalm-param array<string,scalar|Stringable|null> $arguments
     */
    public function __construct(
        public readonly ?string $primaryKey = null,
        public readonly ?string $baseRouteName = null,
        public readonly array $arguments = [],
        public readonly array $queryParameters = [],
        public readonly ?bool $primaryKeyPlace = null,
    ) {
    }
}
