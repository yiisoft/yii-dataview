<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Url;

/**
 * `UrlParameterProviderInterface` defines a contract for retrieving URL parameters.
 */
interface UrlParameterProviderInterface
{
    /**
     * Retrieves a URL parameter value by its name and type.
     *
     * @param string $name The name of the parameter to retrieve.
     * @param UrlParameterType $type The type of the parameter.
     *
     * @return string|null The parameter value if found, `null` otherwise.
     */
    public function get(string $name, UrlParameterType $type): string|null;
}
