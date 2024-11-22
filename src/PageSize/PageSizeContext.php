<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\PageSize;

use Yiisoft\Yii\DataView\BaseListView;

/**
 * @psalm-import-type PageSizeConstraint from BaseListView
 */
final class PageSizeContext
{
    public const URL_PLACEHOLDER = 'YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER';

    /**
     * Page size is fixed to default value.
     */
    public const FIXED_VALUE = true;

    /**
     * Page size could be any value.
     */
    public const ANY_VALUE = false;

    /**
     * @internal
     */
    public function __construct(
        public readonly int $currentValue,
        public readonly int $defaultValue,
        /**
         * @var PageSizeConstraint
         */
        public readonly array|int|bool $constraint,
        public readonly string $urlPattern,
        public readonly string $defaultUrl,
    ) {
    }
}
