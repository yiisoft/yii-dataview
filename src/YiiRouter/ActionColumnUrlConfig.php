<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Stringable;
use Yiisoft\Yii\DataView\UrlParameterType;

final class ActionColumnUrlConfig
{
    /**
     * @param string|null $primaryKey The primary key of the data to be used to generate a URL.
     *
     * @psalm-param array<string,scalar|Stringable|null> $arguments
     * @psalm-param UrlParameterType::*|null $primaryKeyParameterType
     */
    public function __construct(
        public readonly ?string $primaryKey = null,
        public readonly ?string $baseRouteName = null,
        public readonly array $arguments = [],
        public readonly array $queryParameters = [],
        public readonly ?int $primaryKeyParameterType = null,
    ) {
    }
}
