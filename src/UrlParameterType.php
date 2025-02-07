<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

/**
 * UrlParameterType defines constants for different types of URL parameters.
 *
 * This class provides constants that specify how URL parameters should be handled
 * in the data view components. It supports two main types of parameters:
 *
 * - PATH: Parameters that are part of the URL path (e.g., /page/2)
 * - QUERY: Parameters that are part of the query string (e.g., ?page=2)
 *
 * Example usage:
 * ```php
 * // Configure URL parameters to use path format
 * $config = (new UrlConfig())
 *     ->withPageParameterType(UrlParameterType::PATH)
 *     ->withSortParameterType(UrlParameterType::PATH);
 *
 * // Configure URL parameters to use query string format
 * $config = (new UrlConfig())
 *     ->withPageParameterType(UrlParameterType::QUERY)
 *     ->withSortParameterType(UrlParameterType::QUERY);
 * ```
 */
final class UrlParameterType
{
    /**
     * Indicates that a parameter should be included in the URL path.
     *
     * Example: /page/2/sort/name-desc
     */
    public const PATH = 1;

    /**
     * Indicates that a parameter should be included in the query string.
     *
     * Example: ?page=2&sort=name-desc
     */
    public const QUERY = 2;
}
