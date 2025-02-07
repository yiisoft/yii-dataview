<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\PageSize;

use Yiisoft\Yii\DataView\BaseListView;

/**
 * Context class that holds the state and configuration for page size widgets.
 *
 * This class encapsulates all the necessary information for rendering and handling
 * page size controls in data views, including:
 * - Current and default page sizes
 * - Page size constraints
 * - URL patterns for navigation
 *
 * @psalm-import-type PageSizeConstraint from BaseListView
 */
final class PageSizeContext
{
    /**
     * Placeholder used in URL patterns to be replaced with the actual page size value.
     */
    public const URL_PLACEHOLDER = 'YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER';

    /**
     * Constant indicating that the page size is fixed to the default value.
     * When this constraint is used, the page size cannot be changed by the user.
     */
    public const FIXED_VALUE = true;

    /**
     * Constant indicating that any page size value is allowed.
     * When this constraint is used, the user can set any valid page size.
     */
    public const ANY_VALUE = false;

    /**
     * Creates a new page size context.
     *
     * @internal This constructor is meant to be used by the framework only.
     *
     * @param int $currentValue The current page size value.
     * @param int $defaultValue The default page size value.
     * @param array|int|bool $constraint The page size constraint.
     * Can be:
     * - FIXED_VALUE: page size is fixed to default
     * - ANY_VALUE: any page size is allowed
     * - int: maximum allowed page size
     * - array: list of allowed page sizes
     * @param string $urlPattern The URL pattern with placeholder for page size.
     * @param string $defaultUrl The URL for the default page size.
     *
     * @psalm-param PageSizeConstraint $constraint
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
