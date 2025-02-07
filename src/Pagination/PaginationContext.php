<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use Yiisoft\Data\Paginator\PageToken;

/**
 * Context class for pagination widgets that provides URL generation and configuration.
 *
 * This class is responsible for:
 * - Storing URL patterns for pagination navigation
 * - Managing field ordering overrides
 * - Generating URLs for page tokens
 *
 * Example usage:
 * ```php
 * $context = new PaginationContext(
 *     overrideOrderFields: ['id' => 'DESC'],
 *     nextUrlPattern: '/items?next=' . PaginationContext::URL_PLACEHOLDER,
 *     previousUrlPattern: '/items?prev=' . PaginationContext::URL_PLACEHOLDER,
 *     defaultUrl: '/items'
 * );
 *
 * // Generate URL for a next page token
 * $nextUrl = $context->createUrl($nextPageToken);
 * // Result: /items?next=token123
 *
 * // Generate URL for a previous page token
 * $prevUrl = $context->createUrl($prevPageToken);
 * // Result: /items?prev=token456
 * ```
 */
final class PaginationContext
{
    /**
     * Placeholder used in URL patterns that will be replaced with the actual page token.
     */
    public const URL_PLACEHOLDER = 'YII-DATAVIEW-PAGE-PLACEHOLDER';

    /**
     * @internal This constructor is not meant to be used directly.
     * Use the appropriate factory method or dependency injection instead.
     *
     * @param array<string, string> $overrideOrderFields Field ordering overrides.
     * Example: `['id' => 'DESC', 'created_at' => 'ASC']`
     * @param string $nextUrlPattern URL pattern for next page links.
     * Must contain {@see URL_PLACEHOLDER}.
     * @param string $previousUrlPattern URL pattern for previous page links.
     * Must contain {@see URL_PLACEHOLDER}.
     * @param string $defaultUrl Default URL used when no pagination is needed.
     */
    public function __construct(
        public readonly array $overrideOrderFields,
        public readonly string $nextUrlPattern,
        public readonly string $previousUrlPattern,
        public readonly string $defaultUrl,
    ) {
    }

    /**
     * Creates a URL for the given page token.
     *
     * This method replaces the URL_PLACEHOLDER in either the next or previous
     * URL pattern (depending on the token type) with the URL-encoded token value.
     *
     * @param PageToken $pageToken Token for the page.
     *
     * @return string The generated URL with the token value properly encoded.
     */
    public function createUrl(PageToken $pageToken): string
    {
        return str_replace(
            self::URL_PLACEHOLDER,
            urlencode($pageToken->value),
            $pageToken->isPrevious ? $this->previousUrlPattern : $this->nextUrlPattern,
        );
    }
}
