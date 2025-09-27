<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Url;

/**
 * `UrlParameterType` defines constants for different types of URL parameters.
 */
final class UrlParameterType
{
    /**
     * Indicates that a parameter should be included in the URL path such as `/page/2/sort/name-desc`.
     */
    public const PATH = 1;

    /**
     * Indicates that a parameter should be included in the query string such as `?page=2&sort=name-desc`.
     */
    public const QUERY = 2;
}
