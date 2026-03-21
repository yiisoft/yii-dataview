<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Stringable;
use Yiisoft\Yii\DataView\Url\UrlParameterType;

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
     * @param UrlParameterType|null $primaryKeyParameterType How to include the primary key in the URL.
     * @param bool $includeRequestParams Whether to include current request query parameters
     * (such as filters, sorting and pagination) in generated URLs. When `true`, clicking
     * an action button preserves the current grid state (filters, sort, page) so that
     * after the action completes and a redirect happens, the user returns to the same view.
     */
    public function __construct(
        public readonly ?string $primaryKey = null,
        public readonly ?string $baseRouteName = null,
        public readonly array $arguments = [],
        public readonly array $queryParameters = [],
        public readonly ?UrlParameterType $primaryKeyParameterType = null,
        public readonly bool $includeRequestParams = false,
    ) {}
}
