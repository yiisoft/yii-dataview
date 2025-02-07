<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

/**
 * UrlParameterProviderInterface defines a contract for retrieving URL parameters.
 *
 * This interface is used to abstract the way URL parameters are retrieved, allowing for different
 * implementations to handle parameters from various sources such as:
 * - Query string parameters (?page=1)
 * - Path parameters (/page/1)
 * - Request attributes
 * - Custom parameter sources
 *
 * The interface is particularly useful in data views for:
 * - Pagination parameters
 * - Sorting parameters
 * - Filtering parameters
 * - Custom data view parameters
 *
 * Example implementation:
 * ```php
 * class QueryStringParameterProvider implements UrlParameterProviderInterface
 * {
 *     public function __construct(private ServerRequestInterface $request)
 *     {
 *     }
 *
 *     public function get(string $name, int $type): ?string
 *     {
 *         if ($type !== UrlParameterType::QUERY) {
 *             return null;
 *         }
 *
 *         $queryParams = $this->request->getQueryParams();
 *         return $queryParams[$name] ?? null;
 *     }
 * }
 * ```
 *
 * Usage with data views:
 * ```php
 * $provider = new QueryStringParameterProvider($request);
 * $gridView = GridView::widget()
 *     ->urlParameterProvider($provider)
 *     ->dataReader($dataReader);
 * ```
 */
interface UrlParameterProviderInterface
{
    /**
     * Retrieves a URL parameter value by its name and type.
     *
     * This method is responsible for retrieving parameter values based on the parameter type.
     * If a parameter is not found or if the implementation doesn't support the given parameter
     * type, it should return null.
     *
     * @param string $name The name of the parameter to retrieve
     * @param int $type The type of the parameter (query string, path, etc.)
     *
     * @return string|null The parameter value if found, null otherwise
     *
     * @psalm-param UrlParameterType::* $type
     */
    public function get(string $name, int $type): ?string;
}
