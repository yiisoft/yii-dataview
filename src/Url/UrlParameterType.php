<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Url;

/**
 * `UrlParameterType` defines types of URL parameters.
 */
enum UrlParameterType
{
    /**
     * Indicates that a parameter should be included in the URL path such as `/page/2/sort/name-desc`.
     */
    case Path;

    /**
     * Indicates that a parameter should be included in the query string such as `?page=2&sort=name-desc`.
     */
    case Query;
}
