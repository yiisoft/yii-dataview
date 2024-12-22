<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use Yiisoft\Data\Paginator\PageToken;

final class PaginationContext
{
    public const URL_PLACEHOLDER = 'YII-DATAVIEW-PAGE-PLACEHOLDER';

    /**
     * @internal
     *
     * @param array<string, string> $overrideOrderFields
     */
    public function __construct(
        public readonly array $overrideOrderFields,
        public readonly string $nextUrlPattern,
        public readonly string $previousUrlPattern,
        public readonly string $defaultUrl,
    ) {
    }

    /**
     * @param PageToken $pageToken Token for the page.
     * @return string Created URL.
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
