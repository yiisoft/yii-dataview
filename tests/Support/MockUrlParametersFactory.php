<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Yii\DataView\UrlConfig;
use Yiisoft\Yii\DataView\UrlParameterType;

/**
 * A mock implementation of UrlParametersFactory for testing.
 * This class allows us to have full control over the return values.
 */
final class MockUrlParametersFactory
{
    /**
     * Creates URL parameters based on the provided configuration.
     * Only includes parameters that are explicitly set in the test.
     */
    public static function create(
        ?PageToken $pageToken,
        int|string|null $pageSize,
        string|null $sort,
        UrlConfig $context,
    ): array {
        $arguments = [];
        $queryParameters = [];
        
        // Handle page token for next page
        if ($pageToken !== null && !$pageToken->isPrevious) {
            if ($context->getPageParameterType() === UrlParameterType::PATH) {
                $arguments[$context->getPageParameterName()] = $pageToken->value;
            } else {
                $queryParameters[$context->getPageParameterName()] = $pageToken->value;
            }
        }
        
        // Handle page token for previous page
        if ($pageToken !== null && $pageToken->isPrevious) {
            if ($context->getPreviousPageParameterType() === UrlParameterType::PATH) {
                $arguments[$context->getPreviousPageParameterName()] = $pageToken->value;
            } else {
                $queryParameters[$context->getPreviousPageParameterName()] = $pageToken->value;
            }
        }
        
        // Handle page size
        if ($pageSize !== null) {
            if ($context->getPageSizeParameterType() === UrlParameterType::PATH) {
                $arguments[$context->getPageSizeParameterName()] = $pageSize;
            } else {
                $queryParameters[$context->getPageSizeParameterName()] = $pageSize;
            }
        }
        
        // Handle sort
        if ($sort !== null) {
            if ($context->getSortParameterType() === UrlParameterType::PATH) {
                $arguments[$context->getSortParameterName()] = $sort;
            } else {
                $queryParameters[$context->getSortParameterName()] = $sort;
            }
        }
        
        // Add custom arguments and query parameters
        $arguments = array_merge($arguments, $context->getArguments());
        $queryParameters = array_merge($queryParameters, $context->getQueryParameters());
        
        return [$arguments, $queryParameters];
    }
}
