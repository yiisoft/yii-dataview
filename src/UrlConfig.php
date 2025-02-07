<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

/**
 * UrlConfig provides configuration for URL parameter handling in data views.
 *
 * This immutable class manages the configuration of URL parameters used for pagination, sorting,
 * and other data view features. It allows customization of parameter names and types (query string or path),
 * making it flexible for different URL routing strategies.
 *
 * Key features:
 * - Customizable parameter names for page, page size, and sorting
 * - Support for both query string and path parameters
 * - Immutable design with fluent interface
 * - Additional URL arguments and query parameters support
 *
 * The class handles four main types of parameters:
 * 1. Page parameter - For current page number
 * 2. Previous page parameter - For keyset pagination
 * 3. Page size parameter - For items per page
 * 4. Sort parameter - For sorting configuration
 *
 * Example usage:
 * ```php
 * // Basic configuration
 * $config = new UrlConfig();
 *
 * // Customized configuration
 * $config = (new UrlConfig())
 *     ->withPageParameterName('p')
 *     ->withPageParameterType(UrlParameterType::PATH)
 *     ->withSortParameterName('orderBy')
 *     ->withQueryParameters(['category' => 'books']);
 *
 * // Using with GridView
 * $gridView = GridView::widget()
 *     ->urlConfig($config)
 *     ->dataReader($dataReader);
 *
 * // Using with complex URL configuration
 * $config = (new UrlConfig())
 *     // Configure page parameters
 *     ->withPageParameterName('page')
 *     ->withPageParameterType(UrlParameterType::PATH)
 *     ->withPreviousPageParameterName('prev')
 *     ->withPreviousPageParameterType(UrlParameterType::QUERY)
 *
 *     // Configure sorting
 *     ->withSortParameterName('orderBy')
 *     ->withSortParameterType(UrlParameterType::QUERY)
 *
 *     // Configure page size
 *     ->withPageSizeParameterName('limit')
 *     ->withPageSizeParameterType(UrlParameterType::QUERY)
 *
 *     // Add additional parameters
 *     ->withArguments(['category' => 'books'])
 *     ->withQueryParameters(['filter' => 'active']);
 *
 * // Resulting URLs might look like:
 * // /books/page/2?orderBy=title&limit=20&filter=active
 * // /books/page/3?prev=2&orderBy=-created&limit=20&filter=active
 * ```
 *
 * @psalm-import-type UrlArguments from BaseListView
 * @psalm-immutable
 */
final class UrlConfig
{
    /**
     * Initializes a new instance of URL configuration.
     *
     * This constructor sets up the initial configuration for URL parameters. Each parameter
     * can be customized using the corresponding `with*` methods.
     *
     * Example:
     * ```php
     * // Basic configuration with defaults
     * $config = new UrlConfig();
     *
     * // Custom configuration
     * $config = new UrlConfig(
     *     pageParameterName: 'p',
     *     pageSizeParameterName: 'limit',
     *     sortParameterName: 'orderBy',
     *     pageParameterType: UrlParameterType::PATH,
     *     arguments: ['category' => 'books'],
     *     queryParameters: ['filter' => 'active']
     * );
     * ```
     *
     * @param string $pageParameterName Name of the parameter for the current page number.
     *                                 Used in URLs to specify which page to display.
     *                                 Default: 'page'
     *
     * @param string $previousPageParameterName Name of the parameter for the previous page.
     *                                         Used in keyset pagination to maintain state.
     *                                         Default: 'prev-page'
     *
     * @param string $pageSizeParameterName Name of the parameter for items per page.
     *                                     Controls how many items to show per page.
     *                                     Default: 'pagesize'
     *
     * @param string $sortParameterName Name of the parameter for sorting configuration.
     *                                 Used to specify sort field and direction.
     *                                 Default: 'sort'
     *
     * @param int $pageParameterType Type of the page parameter (query or path).
     *                              Controls whether page number appears in path or query.
     *                              Default: UrlParameterType::QUERY
     *
     * @param int $previousPageParameterType Type of the previous page parameter.
     *                                      Controls how previous page token is passed.
     *                                      Default: UrlParameterType::QUERY
     *
     * @param int $pageSizeParameterType Type of the page size parameter.
     *                                  Controls how items per page is specified.
     *                                  Default: UrlParameterType::QUERY
     *
     * @param int $sortParameterType Type of the sort parameter.
     *                              Controls how sorting is specified in URL.
     *                              Default: UrlParameterType::QUERY
     *
     * @param array $arguments Additional URL arguments to be included in generated URLs.
     *                       These are typically used in the path portion.
     *                       Default: []
     *
     * @param array $queryParameters Additional query parameters to be included in generated URLs.
     *                             These are always added as query parameters.
     *                             Default: []
     *
     * @psalm-var UrlParameterType::* $pageParameterType
     * @psalm-var UrlParameterType::* $previousPageParameterType
     * @psalm-var UrlParameterType::* $pageSizeParameterType
     * @psalm-var UrlParameterType::* $sortParameterType
     * @psalm-var UrlArguments $arguments
     */
    public function __construct(
        private string $pageParameterName = 'page',
        private string $previousPageParameterName = 'prev-page',
        private string $pageSizeParameterName = 'pagesize',
        private string $sortParameterName = 'sort',
        private int $pageParameterType = UrlParameterType::QUERY,
        private int $previousPageParameterType = UrlParameterType::QUERY,
        private int $pageSizeParameterType = UrlParameterType::QUERY,
        private int $sortParameterType = UrlParameterType::QUERY,
        private array $arguments = [],
        private array $queryParameters = [],
    ) {
    }

    /**
     * Creates a new instance with the specified page parameter name.
     *
     * This method is used to customize the name of the parameter that represents
     * the current page number in URLs.
     *
     * Example:
     * ```php
     * // Change page parameter from default 'page' to 'p'
     * $config = $config->withPageParameterName('p');
     * // Result: ?p=2 instead of ?page=2
     * ```
     *
     * @param string $name The new page parameter name. Common values include
     *                    'page', 'p', 'pg', etc.
     *
     * @return self A new instance with the updated page parameter name.
     */
    public function withPageParameterName(string $name): self
    {
        $new = clone $this;
        $new->pageParameterName = $name;
        return $new;
    }

    /**
     * Creates a new instance with the specified previous page parameter name.
     *
     * This method is used to customize the name of the parameter that holds
     * the previous page token in keyset pagination.
     *
     * Example:
     * ```php
     * // Change previous page parameter name
     * $config = $config->withPreviousPageParameterName('prev');
     * // Result: ?prev=token123 instead of ?prev-page=token123
     * ```
     *
     * @param string $name The new previous page parameter name. Common values
     *                    include 'prev-page', 'prev', 'before', etc.
     *
     * @return self A new instance with the updated previous page parameter name.
     */
    public function withPreviousPageParameterName(string $name): self
    {
        $new = clone $this;
        $new->previousPageParameterName = $name;
        return $new;
    }

    /**
     * Creates a new instance with the specified page size parameter name.
     *
     * This method is used to customize the name of the parameter that controls
     * how many items are displayed per page.
     *
     * Example:
     * ```php
     * // Change page size parameter name
     * $config = $config->withPageSizeParameterName('limit');
     * // Result: ?limit=20 instead of ?pagesize=20
     * ```
     *
     * @param string $pageSizeParameterName The new page size parameter name.
     *                                     Common values include 'pagesize',
     *                                     'limit', 'per-page', etc.
     *
     * @return self A new instance with the updated page size parameter name.
     */
    public function withPageSizeParameterName(string $pageSizeParameterName): self
    {
        $new = clone $this;
        $new->pageSizeParameterName = $pageSizeParameterName;
        return $new;
    }

    /**
     * Creates a new instance with the specified sort parameter name.
     *
     * This method is used to customize the name of the parameter that specifies
     * the sorting configuration in URLs.
     *
     * Example:
     * ```php
     * // Change sort parameter name
     * $config = $config->withSortParameterName('orderBy');
     * // Result: ?orderBy=name,-created instead of ?sort=name,-created
     * ```
     *
     * @param string $sortParameterName The new sort parameter name. Common values
     *                                include 'sort', 'orderBy', 'order', etc.
     *
     * @return self A new instance with the updated sort parameter name.
     */
    public function withSortParameterName(string $sortParameterName): self
    {
        $new = clone $this;
        $new->sortParameterName = $sortParameterName;
        return $new;
    }

    /**
     * Creates a new instance with the specified page parameter type.
     *
     * This method controls whether the page parameter appears in the URL path
     * or as a query parameter.
     *
     * Example:
     * ```php
     * // Change page parameter to appear in path
     * $config = $config->withPageParameterType(UrlParameterType::PATH);
     * // Result: /page/2 instead of ?page=2
     *
     * // Change page parameter to appear in query
     * $config = $config->withPageParameterType(UrlParameterType::QUERY);
     * // Result: ?page=2 instead of /page/2
     * ```
     *
     * @param int $type The new page parameter type. Must be one of:
     *                 - UrlParameterType::PATH for path parameters
     *                 - UrlParameterType::QUERY for query parameters
     *
     * @return self A new instance with the updated page parameter type.
     *
     * @psalm-param UrlParameterType::* $type
     */
    public function withPageParameterType(int $type): self
    {
        $new = clone $this;
        $new->pageParameterType = $type;
        return $new;
    }

    /**
     * Creates a new instance with the specified previous page parameter type.
     *
     * This method controls whether the previous page parameter appears in the URL
     * path or as a query parameter when using keyset pagination.
     *
     * Example:
     * ```php
     * // Change previous page parameter to appear in path
     * $config = $config->withPreviousPageParameterType(UrlParameterType::PATH);
     * // Result: /prev/token123 instead of ?prev=token123
     *
     * // Change previous page parameter to appear in query
     * $config = $config->withPreviousPageParameterType(UrlParameterType::QUERY);
     * // Result: ?prev=token123 instead of /prev/token123
     * ```
     *
     * @param int $type The new previous page parameter type. Must be one of:
     *                 - UrlParameterType::PATH for path parameters
     *                 - UrlParameterType::QUERY for query parameters
     *
     * @return self A new instance with the updated previous page parameter type.
     *
     * @psalm-param UrlParameterType::* $type
     */
    public function withPreviousPageParameterType(int $type): self
    {
        $new = clone $this;
        $new->previousPageParameterType = $type;
        return $new;
    }

    /**
     * Creates a new instance with the specified page size parameter type.
     *
     * This method controls whether the page size parameter appears in the URL
     * path or as a query parameter.
     *
     * Example:
     * ```php
     * // Change page size parameter to appear in path
     * $config = $config->withPageSizeParameterType(UrlParameterType::PATH);
     * // Result: /limit/20 instead of ?limit=20
     *
     * // Change page size parameter to appear in query
     * $config = $config->withPageSizeParameterType(UrlParameterType::QUERY);
     * // Result: ?limit=20 instead of /limit/20
     * ```
     *
     * @param int $type The new page size parameter type. Must be one of:
     *                 - UrlParameterType::PATH for path parameters
     *                 - UrlParameterType::QUERY for query parameters
     *
     * @return self A new instance with the updated page size parameter type.
     *
     * @psalm-param UrlParameterType::* $type
     */
    public function withPageSizeParameterType(int $type): self
    {
        $new = clone $this;
        $new->pageSizeParameterType = $type;
        return $new;
    }

    /**
     * Creates a new instance with the specified sort parameter type.
     *
     * This method controls whether the sort parameter appears in the URL
     * path or as a query parameter.
     *
     * Example:
     * ```php
     * // Change sort parameter to appear in path
     * $config = $config->withSortParameterType(UrlParameterType::PATH);
     * // Result: /sort/name,-created instead of ?sort=name,-created
     *
     * // Change sort parameter to appear in query
     * $config = $config->withSortParameterType(UrlParameterType::QUERY);
     * // Result: ?sort=name,-created instead of /sort/name,-created
     * ```
     *
     * @param int $type The new sort parameter type. Must be one of:
     *                 - UrlParameterType::PATH for path parameters
     *                 - UrlParameterType::QUERY for query parameters
     *
     * @return self A new instance with the updated sort parameter type.
     *
     * @psalm-param UrlParameterType::* $type
     */
    public function withSortParameterType(int $type): self
    {
        $new = clone $this;
        $new->sortParameterType = $type;
        return $new;
    }

    /**
     * Creates a new instance with the specified additional URL arguments.
     *
     * This method is used to add fixed arguments that will be included in the URL path.
     * These are typically used for route parameters or other path segments.
     *
     * Example:
     * ```php
     * // Add category and language to URL path
     * $config = $config->withArguments([
     *     'category' => 'books',
     *     'language' => 'en'
     * ]);
     * // Result: /books/en/page/2 instead of /page/2
     * ```
     *
     * @param array $arguments The new additional URL arguments. These will completely
     *                       replace any existing arguments.
     *
     * @return self A new instance with the updated additional URL arguments.
     *
     * @psalm-param UrlArguments $arguments
     */
    public function withArguments(array $arguments): self
    {
        $new = clone $this;
        $new->arguments = $arguments;
        return $new;
    }

    /**
     * Creates a new instance with the specified additional query parameters.
     *
     * This method is used to add fixed query parameters that will be included
     * in all generated URLs. These are always added as query string parameters,
     * regardless of other parameter type settings.
     *
     * Example:
     * ```php
     * // Add filter and view mode to query string
     * $config = $config->withQueryParameters([
     *     'filter' => 'active',
     *     'view' => 'grid'
     * ]);
     * // Result: ?page=2&filter=active&view=grid
     * ```
     *
     * @param array $queryParameters The new additional query parameters. These will
     *                             completely replace any existing query parameters.
     *
     * @return self A new instance with the updated additional query parameters.
     */
    public function withQueryParameters(array $queryParameters): self
    {
        $new = clone $this;
        $new->queryParameters = $queryParameters;
        return $new;
    }

    /**
     * Returns the page parameter name.
     *
     * This is the name used in URLs to specify which page to display.
     *
     * @return string The page parameter name (e.g., 'page', 'p').
     */
    public function getPageParameterName(): string
    {
        return $this->pageParameterName;
    }

    /**
     * Returns the previous page parameter name.
     *
     * This is the name used in URLs for keyset pagination to specify
     * the previous page token.
     *
     * @return string The previous page parameter name (e.g., 'prev-page', 'prev').
     */
    public function getPreviousPageParameterName(): string
    {
        return $this->previousPageParameterName;
    }

    /**
     * Returns the page size parameter name.
     *
     * This is the name used in URLs to specify how many items to display per page.
     *
     * @return string The page size parameter name (e.g., 'pagesize', 'limit').
     */
    public function getPageSizeParameterName(): string
    {
        return $this->pageSizeParameterName;
    }

    /**
     * Returns the sort parameter name.
     *
     * This is the name used in URLs to specify the sorting configuration.
     *
     * @return string The sort parameter name (e.g., 'sort', 'orderBy').
     */
    public function getSortParameterName(): string
    {
        return $this->sortParameterName;
    }

    /**
     * Returns the page parameter type.
     *
     * This determines whether the page number appears in the URL path
     * or as a query parameter.
     *
     * @return int The page parameter type (UrlParameterType::PATH or QUERY).
     *
     * @psalm-return UrlParameterType::*
     */
    public function getPageParameterType(): int
    {
        return $this->pageParameterType;
    }

    /**
     * Returns the previous page parameter type.
     *
     * This determines whether the previous page token appears in the URL path
     * or as a query parameter when using keyset pagination.
     *
     * @return int The previous page parameter type (UrlParameterType::PATH or QUERY).
     *
     * @psalm-return UrlParameterType::*
     */
    public function getPreviousPageParameterType(): int
    {
        return $this->previousPageParameterType;
    }

    /**
     * Returns the page size parameter type.
     *
     * This determines whether the items per page setting appears in the URL path
     * or as a query parameter.
     *
     * @return int The page size parameter type (UrlParameterType::PATH or QUERY).
     *
     * @psalm-return UrlParameterType::*
     */
    public function getPageSizeParameterType(): int
    {
        return $this->pageSizeParameterType;
    }

    /**
     * Returns the sort parameter type.
     *
     * This determines whether the sorting configuration appears in the URL path
     * or as a query parameter.
     *
     * @return int The sort parameter type (UrlParameterType::PATH or QUERY).
     *
     * @psalm-return UrlParameterType::*
     */
    public function getSortParameterType(): int
    {
        return $this->sortParameterType;
    }

    /**
     * Returns the additional URL arguments.
     *
     * These are fixed arguments that are included in the URL path.
     *
     * @return array The additional URL arguments as key-value pairs.
     *
     * @psalm-return UrlArguments
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Returns the additional query parameters.
     *
     * These are fixed parameters that are always included in the query string.
     *
     * @return array The additional query parameters as key-value pairs.
     */
    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }
}
