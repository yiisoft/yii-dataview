<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Yiisoft\Data\Paginator\PageToken;

/**
 * Factory for creating URL parameters for data view components.
 *
 * @internal This class is used internally by data view components.
 *
 * @psalm-import-type UrlArguments from BaseListView
 */
final class UrlParametersFactory
{
    /**
     * Create URL parameters based on the current data view state and configuration.
     *
     * @param PageToken|null $pageToken Current page token for pagination. Contains
     * both the token value and direction (next/previous).
     * @param int|string|null $pageSize Number of items per page. Can be `null` to use
     * the default page size.
     * @param string|null $sort Current sort expression (e.g., 'name,-created_at').
     * Null means no sorting.
     * @param UrlConfig $context URL configuration that defines parameter names and types.
     * Controls how parameters are included in the URL.
     *
     * @psalm-return list{UrlArguments, array}
     * @return array Two-element array containing:
     * - Path arguments as key-value pairs
     * - Query parameters as key-value pairs
     */
    /**
     * @return array{0: array<string, string|int|null>, 1: array<string, string|int|null>}
     */
    public static function create(
        ?PageToken $pageToken,
        int|string|null $pageSize,
        string|null $sort,
        UrlConfig $context,
    ): array {
        $arguments = $context->getArguments();
        $queryParameters = $context->getQueryParameters();

        switch ($context->getPageParameterType()) {
            case UrlParameterType::PATH:
                /**
                 * @psalm-suppress PossiblyNullPropertyFetch https://github.com/vimeo/psalm/issues/10591
                 */
                $arguments[$context->getPageParameterName()] = $pageToken?->isPrevious === false
                    ? $pageToken->value
                    : null;
                break;
            case UrlParameterType::QUERY:
                /**
                 * @psalm-suppress PossiblyNullPropertyFetch https://github.com/vimeo/psalm/issues/10591
                 */
                $queryParameters[$context->getPageParameterName()] = $pageToken?->isPrevious === false
                    ? $pageToken->value
                    : null;
                break;
        }

        switch ($context->getPreviousPageParameterType()) {
            case UrlParameterType::PATH:
                /**
                 * @psalm-suppress PossiblyNullPropertyFetch https://github.com/vimeo/psalm/issues/10591
                 */
                $arguments[$context->getPreviousPageParameterName()] = $pageToken?->isPrevious === true
                    ? $pageToken->value
                    : null;
                break;
            case UrlParameterType::QUERY:
                /**
                 * @psalm-suppress PossiblyNullPropertyFetch https://github.com/vimeo/psalm/issues/10591
                 */
                $queryParameters[$context->getPreviousPageParameterName()] = $pageToken?->isPrevious === true
                    ? $pageToken->value
                    : null;
                break;
        }

        switch ($context->getPageSizeParameterType()) {
            case UrlParameterType::PATH:
                $arguments[$context->getPageSizeParameterName()] = $pageSize;
                break;
            case UrlParameterType::QUERY:
                $queryParameters[$context->getPageSizeParameterName()] = $pageSize;
                break;
        }

        switch ($context->getSortParameterType()) {
            case UrlParameterType::PATH:
                $arguments[$context->getSortParameterName()] = $sort;
                break;
            case UrlParameterType::QUERY:
                $queryParameters[$context->getSortParameterName()] = $sort;
                break;
        }

        return [$arguments, $queryParameters];
    }
}
