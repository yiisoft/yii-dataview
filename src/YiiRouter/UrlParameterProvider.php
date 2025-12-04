<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\DataView\Url\UrlParameterProviderInterface;
use Yiisoft\Yii\DataView\Url\UrlParameterType;

use function is_string;

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
    public function __construct(private readonly CurrentRoute $currentRoute) {}

    /**
     * Gets a URL parameter value by name and type.
     *
     * This method can retrieve parameters from:
     * - Path: Using route arguments (e.g., /users/{id}/profile)
     * - Query: Using $_GET parameters (e.g., ?sort=name)
     *
     * @param string $name The parameter name to retrieve.
     * @param UrlParameterType $type The parameter type.
     *
     * @return string|null The parameter value if found, `null` otherwise.
     * For query parameters, only string values are returned; other types result in `null`.
     */
    public function get(string $name, UrlParameterType $type): ?string
    {
        return match ($type) {
            UrlParameterType::Path => $this->currentRoute->getArgument($name),
            UrlParameterType::Query => (isset($_GET[$name]) && is_string($_GET[$name])) ? $_GET[$name] : null,
        };
    }
}
