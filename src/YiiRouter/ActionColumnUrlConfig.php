<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Stringable;
use Yiisoft\Yii\DataView\UrlParameterType;

/**
 * Configuration class for generating URLs in action columns.
 */
final class ActionColumnUrlConfig
{
    /**
     * Creates a new URL configuration instance.
     *
     * @param string|null $primaryKey The primary key field name used to generate URLs.
     * @param string|null $baseRouteName The base route name for generating URLs.
     * @param array $arguments Additional route arguments to include in the URL.
     * @psalm-param array<string,scalar|Stringable|null> $arguments
     * @param array $queryParameters Additional query parameters to append to the URL.
     * @param int|null $primaryKeyParameterType How to include the primary key in the URL.
     * Use constants from {@see UrlParameterType}.
     * - {@see UrlParameterType::PATH} for /user/view/123
     * - {@see UrlParameterType::QUERY} for /user/view?id=123
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
