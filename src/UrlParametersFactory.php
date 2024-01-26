<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Yiisoft\Data\Paginator\PageToken;

/**
 * @internal
 *
 * @psalm-import-type UrlArguments from BaseListView
 */
final class UrlParametersFactory
{
    /**
     * @psalm-return list{UrlArguments, array}
     */
    public static function create(
        ?PageToken $pageToken,
        int|null $pageSize,
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
