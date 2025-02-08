<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\DataView\UrlParameterType;
use Yiisoft\Yii\DataView\UrlParameterProviderInterface;

/**
 * Provider for accessing URL parameters from both path and query string.
 */
final class UrlParameterProvider implements UrlParameterProviderInterface
{
    /**
     * Creates a new URL parameter provider instance.
     *
     * @param CurrentRoute $currentRoute The current route service used to
     * access route arguments.
     */
    public function __construct(private readonly CurrentRoute $currentRoute)
    {
    }

    /**
     * Gets a URL parameter value by name and type.
     *
     * This method can retrieve parameters from:
     * - Path: Using route arguments (e.g., /users/{id}/profile)
     * - Query: Using $_GET parameters (e.g., ?sort=name)
     *
     * @param string $name The parameter name to retrieve.
     * @param int $type The parameter type, use constants from {@see UrlParameterType}.
     * - {@see UrlParameterType::PATH} for route arguments
     * - {@see UrlParameterType::QUERY} for query parameters
     *
     * @return string|null The parameter value if found, `null` otherwise.
     * For query parameters, only string values are returned; other types result in `null`.
     */
    public function get(string $name, int $type): ?string
    {
        switch ($type) {
            case UrlParameterType::PATH:
                return $this->currentRoute->getArgument($name);
            case UrlParameterType::QUERY:
                $value = $_GET[$name] ?? null;
                return is_string($value) ? $value : null;
            default:
                return null;
        }
    }
}
