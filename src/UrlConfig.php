<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

/**
 * @psalm-import-type UrlArguments from BaseListView
 * @psalm-immutable
 */
final class UrlConfig
{
    public function __construct(
        private string $pageParameterName = 'page',
        private string $previousPageParameterName = 'prev-page',
        private string $pageSizeParameterName = 'pagesize',
        private string $sortParameterName = 'sort',

        /**
         * @psalm-var UrlParameterType::*
         */
        private int $pageParameterType = UrlParameterType::QUERY,

        /**
         * @psalm-var UrlParameterType::*
         */
        private int $previousPageParameterType = UrlParameterType::QUERY,

        /**
         * @psalm-var UrlParameterType::*
         */
        private int $pageSizeParameterType = UrlParameterType::QUERY,

        /**
         * @psalm-var UrlParameterType::*
         */
        private int $sortParameterType = UrlParameterType::QUERY,

        /**
         * @psalm-var UrlArguments
         */
        private array $arguments = [],
        private array $queryParameters = [],
    ) {
    }

    public function withPageParameterName(string $name): self
    {
        $new = clone $this;
        $new->pageParameterName = $name;
        return $new;
    }

    public function withPreviousPageParameterName(string $name): self
    {
        $new = clone $this;
        $new->previousPageParameterName = $name;
        return $new;
    }

    public function withPageSizeParameterName(string $pageSizeParameterName): self
    {
        $new = clone $this;
        $new->pageSizeParameterName = $pageSizeParameterName;
        return $new;
    }

    public function withSortParameterName(string $sortParameterName): self
    {
        $new = clone $this;
        $new->sortParameterName = $sortParameterName;
        return $new;
    }

    /**
     * @psalm-param UrlParameterType::* $type
     */
    public function withPageParameterType(int $type): self
    {
        $new = clone $this;
        $new->pageParameterType = $type;
        return $new;
    }

    /**
     * @psalm-param UrlParameterType::* $type
     */
    public function withPreviousPageParameterType(int $type): self
    {
        $new = clone $this;
        $new->previousPageParameterType = $type;
        return $new;
    }

    /**
     * @psalm-param UrlParameterType::* $type
     */
    public function withPageSizeParameterType(int $type): self
    {
        $new = clone $this;
        $new->pageSizeParameterType = $type;
        return $new;
    }

    /**
     * @psalm-param UrlParameterType::* $type
     */
    public function withSortParameterType(int $type): self
    {
        $new = clone $this;
        $new->sortParameterType = $type;
        return $new;
    }

    /**
     * @psalm-param UrlArguments $arguments
     */
    public function withArguments(array $arguments): self
    {
        $new = clone $this;
        $new->arguments = $arguments;
        return $new;
    }

    public function withQueryParameters(array $queryParameters): self
    {
        $new = clone $this;
        $new->queryParameters = $queryParameters;
        return $new;
    }

    public function getPageParameterName(): string
    {
        return $this->pageParameterName;
    }

    public function getPreviousPageParameterName(): string
    {
        return $this->previousPageParameterName;
    }

    public function getPageSizeParameterName(): string
    {
        return $this->pageSizeParameterName;
    }

    public function getSortParameterName(): string
    {
        return $this->sortParameterName;
    }

    /**
     * @psalm-return UrlParameterType::*
     */
    public function getPageParameterType(): int
    {
        return $this->pageParameterType;
    }

    /**
     * @psalm-return UrlParameterType::*
     */
    public function getPreviousPageParameterType(): int
    {
        return $this->previousPageParameterType;
    }

    /**
     * @psalm-return UrlParameterType::*
     */
    public function getPageSizeParameterType(): int
    {
        return $this->pageSizeParameterType;
    }

    /**
     * @psalm-return UrlParameterType::*
     */
    public function getSortParameterType(): int
    {
        return $this->sortParameterType;
    }

    /**
     * @psalm-return UrlArguments
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }
}
