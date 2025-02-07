<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Stringable;
use Yiisoft\Yii\DataView\UrlParameterType;

/**
 * Configuration class for generating URLs in action columns.
 *
 * This class provides configuration options for generating URLs in GridView action
 * columns. It supports:
 * - Primary key handling
 * - Base route configuration
 * - Additional route arguments
 * - Query parameters
 * - Parameter type customization
 *
 * Example usage:
 * ```php
 * // Basic configuration for view/edit/delete actions
 * $viewConfig = new ActionColumnUrlConfig(
 *     primaryKey: 'id',
 *     baseRouteName: 'user/view',
 *     arguments: ['tenant' => 'main'],
 *     queryParameters: ['tab' => 'profile'],
 *     primaryKeyParameterType: UrlParameterType::QUERY
 * );
 *
 * // Will generate URLs like: /user/view?tenant=main&id=123&tab=profile
 *
 * // Configuration for path-based URLs
 * $editConfig = new ActionColumnUrlConfig(
 *     primaryKey: 'id',
 *     baseRouteName: 'user/edit',
 *     primaryKeyParameterType: UrlParameterType::PATH
 * );
 *
 * // Will generate URLs like: /user/edit/123
 * ```
 */
final class ActionColumnUrlConfig
{
    /**
     * Creates a new URL configuration instance.
     *
     * @param string|null $primaryKey The primary key field name used to generate URLs.
     * Its value will be extracted from the data row.
     * Example: 'id', 'uuid', etc.
     *
     * @param string|null $baseRouteName The base route name for generating URLs.
     * Example: 'user/view', 'product/edit', etc.
     *
     * @param array $arguments Additional route arguments to include in the URL.
     * These are static values that don't depend on the data row.
     * Example: `['tenant' => 'main', 'version' => 2]`
     * @psalm-param array<string,scalar|Stringable|null> $arguments
     *
     * @param array $queryParameters Additional query parameters to append to the URL.
     * Example: `['tab' => 'profile', 'mode' => 'advanced']`
     *
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
