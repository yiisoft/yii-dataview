<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\BaseListView;

/**
 * URL creator for list views that generates URLs based on the current route.
 *
 * This class is responsible for generating URLs while preserving the current route
 * and allowing modifications to route arguments and query parameters. It's commonly
 * used in:
 * - Pagination links
 * - Sort links
 * - Filter links
 * - Page size selectors
 *
 * Example usage:
 * ```php
 * // Create the URL creator
 * $urlCreator = new UrlCreator($container->get(UrlGeneratorInterface::class));
 *
 * // Generate a URL for page 2 with additional filters
 * $url = $urlCreator(
 *     arguments: ['page' => 2],
 *     queryParameters: ['category' => 'books', 'inStock' => true]
 * );
 * // Result: /current/route/page/2?category=books&inStock=1
 *
 * // Generate a URL for sorting
 * $url = $urlCreator(
 *     arguments: [],
 *     queryParameters: ['sort' => '-created_at']
 * );
 * // Result: /current/route?sort=-created_at
 * ```
 *
 * @psalm-import-type UrlArguments from BaseListView
 */
final class UrlCreator
{
    /**
     * Creates a new URL creator instance.
     *
     * @param UrlGeneratorInterface $urlGenerator The URL generator service used
     * to generate URLs from the current route.
     */
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * Generates a URL based on the current route with modified parameters.
     *
     * This method preserves the current route while allowing you to:
     * - Override route arguments (path parameters)
     * - Add or modify query parameters
     *
     * @param array $arguments Route arguments to override in the current route.
     * Example: `['page' => 2, 'category' => 'books']`
     * @psalm-param UrlArguments $arguments
     *
     * @param array $queryParameters Query parameters to append to the URL.
     * Example: `['sort' => '-created_at', 'filter' => 'active']`
     *
     * @return string The generated URL with the specified modifications.
     */
    public function __invoke(array $arguments, array $queryParameters): string
    {
        return $this->urlGenerator->generateFromCurrent($arguments, $queryParameters);
    }
}
