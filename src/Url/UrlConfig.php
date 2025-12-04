<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Url;

use Stringable as UrlArguments;
use Yiisoft\Yii\DataView\BaseListView;

/**
 * `UrlConfig` provides configuration for URL parameter handling in data views.
 *
 * @psalm-import-type UrlArguments from BaseListView
 * @psalm-immutable
 */
final class UrlConfig
{
    /**
     * @param string $pageParameterName Name of the parameter for the current page number.
     * @param string $previousPageParameterName Name of the parameter for the previous page.
     * @param string $pageSizeParameterName Name of the parameter for items per page.
     * @param string $sortParameterName Name of the parameter for sorting configuration.
     * @param UrlParameterType $pageParameterType Type of the page parameter (query or path).
     * @param UrlParameterType $previousPageParameterType Type of the previous page parameter.
     * @param UrlParameterType $pageSizeParameterType Type of the page size parameter.
     * @param UrlParameterType $sortParameterType Type of the sort parameter.
     * @param array $arguments Additional URL arguments to be included in generated URLs.
     * @psalm-param UrlArguments $arguments
     * @param array $queryParameters Additional query parameters to be included in generated URLs.
     */
    public function __construct(
        private string $pageParameterName = 'page',
        private string $previousPageParameterName = 'prev-page',
        private string $pageSizeParameterName = 'pagesize',
        private string $sortParameterName = 'sort',
        private UrlParameterType $pageParameterType = UrlParameterType::Query,
        private UrlParameterType $previousPageParameterType = UrlParameterType::Query,
        private UrlParameterType $pageSizeParameterType = UrlParameterType::Query,
        private UrlParameterType $sortParameterType = UrlParameterType::Query,
        private array $arguments = [],
        private array $queryParameters = [],
    ) {}

    /**
     * Creates a new instance with the specified page parameter name.
     *
     * @param string $name The new page parameter name.
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
     * @param string $name The new previous page parameter name.
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
     * @param string $name The new page size parameter name.
     *
     * @return self A new instance with the updated page size parameter name.
     */
    public function withPageSizeParameterName(string $name): self
    {
        $new = clone $this;
        $new->pageSizeParameterName = $name;
        return $new;
    }

    /**
     * Creates a new instance with the specified sort parameter name.
     *
     * @param string $name The new sort parameter name.
     *
     * @return self A new instance with the updated sort parameter name.
     */
    public function withSortParameterName(string $name): self
    {
        $new = clone $this;
        $new->sortParameterName = $name;
        return $new;
    }

    /**
     * Creates a new instance with the specified page parameter type.
     *
     * @param UrlParameterType $type The new page parameter type.
     *
     * @return self A new instance with the updated page parameter type.
     */
    public function withPageParameterType(UrlParameterType $type): self
    {
        $new = clone $this;
        $new->pageParameterType = $type;
        return $new;
    }

    /**
     * Creates a new instance with the specified previous page parameter type.
     *
     * @param UrlParameterType $type The new previous page parameter type.
     *
     * @return self A new instance with the updated previous page parameter type.
     */
    public function withPreviousPageParameterType(UrlParameterType $type): self
    {
        $new = clone $this;
        $new->previousPageParameterType = $type;
        return $new;
    }

    /**
     * Creates a new instance with the specified page size parameter type.
     *
     * @param UrlParameterType $type The new page size parameter type.
     *
     * @return self A new instance with the updated page size parameter type.
     */
    public function withPageSizeParameterType(UrlParameterType $type): self
    {
        $new = clone $this;
        $new->pageSizeParameterType = $type;
        return $new;
    }

    /**
     * Creates a new instance with the specified sort parameter type.
     *
     * @param UrlParameterType $type The new sort parameter type.
     *
     * @return self A new instance with the updated sort parameter type.
     */
    public function withSortParameterType(UrlParameterType $type): self
    {
        $new = clone $this;
        $new->sortParameterType = $type;
        return $new;
    }

    /**
     * Creates a new instance with the specified additional URL arguments.
     *
     * @param array $arguments The new additional URL arguments. These will
     * completely replace any existing arguments.
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
     * @param array $parameters The new additional query parameters. These will
     * completely replace any existing query parameters.
     *
     * @return self A new instance with the updated additional query parameters.
     */
    public function withQueryParameters(array $parameters): self
    {
        $new = clone $this;
        $new->queryParameters = $parameters;
        return $new;
    }

    /**
     * Returns the page parameter name.
     *
     * @return string The page parameter name.
     */
    public function getPageParameterName(): string
    {
        return $this->pageParameterName;
    }

    /**
     * Returns the previous page parameter name.
     *
     * @return string The previous page parameter name.
     */
    public function getPreviousPageParameterName(): string
    {
        return $this->previousPageParameterName;
    }

    /**
     * Returns the page size parameter name.
     *
     * @return string The page size parameter name.
     */
    public function getPageSizeParameterName(): string
    {
        return $this->pageSizeParameterName;
    }

    /**
     * Returns the sort parameter name.
     *
     * @return string The sort parameter name.
     */
    public function getSortParameterName(): string
    {
        return $this->sortParameterName;
    }

    /**
     * Returns the page parameter type.
     *
     * @return UrlParameterType The page parameter type.
     */
    public function getPageParameterType(): UrlParameterType
    {
        return $this->pageParameterType;
    }

    /**
     * Returns the previous page parameter type.
     *
     * @return UrlParameterType The previous page parameter type.
     */
    public function getPreviousPageParameterType(): UrlParameterType
    {
        return $this->previousPageParameterType;
    }

    /**
     * Returns the page size parameter type.
     *
     * @return UrlParameterType The page size parameter type.
     */
    public function getPageSizeParameterType(): UrlParameterType
    {
        return $this->pageSizeParameterType;
    }

    /**
     * Returns the sort parameter type.
     *
     * @return UrlParameterType The sort parameter type.
     */
    public function getSortParameterType(): UrlParameterType
    {
        return $this->sortParameterType;
    }

    /**
     * Returns the additional URL arguments.
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
     * @return array The additional query parameters as key-value pairs.
     */
    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }
}
