<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use Yiisoft\Data\Paginator\PageToken;

/**
 * Context class for pagination widgets that provides URL generation and configuration.
 */
final class PaginationContext
{
    /**
     * Placeholder used in URL patterns that will be replaced with the actual page token.
     */
    public const URL_PLACEHOLDER = 'YII-DATAVIEW-PAGE-PLACEHOLDER';

    /**
     * @param string $nextUrlPattern URL pattern for next page links. Must contain {@see URL_PLACEHOLDER}.
     * @param string $previousUrlPattern URL pattern for previous page links. Must contain {@see URL_PLACEHOLDER}.
     * @param string $firstPageUrl URL used on the first page.
     */
    public function __construct(
        public readonly string $nextUrlPattern,
        public readonly string $previousUrlPattern,
        public readonly string $firstPageUrl,
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
