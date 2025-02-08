<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

/**
 * `UrlParameterProviderInterface` defines a contract for retrieving URL parameters.
 */
interface UrlParameterProviderInterface
{
    /**
     * Retrieves a URL parameter value by its name and type.
     *
     * @param string $name The name of the parameter to retrieve.
     * @param int $type The type of the parameter (query string, path, etc.).
     *
     * @return string|null The parameter value if found, `null` otherwise.
     *
     * @psalm-param UrlParameterType::* $type
     */
    public function get(string $name, int $type): ?string;
}
