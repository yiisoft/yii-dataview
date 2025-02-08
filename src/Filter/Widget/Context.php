<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Widget;

/**
 * Context for filter widgets that provides necessary data for rendering filter inputs.
 */
final class Context
{
    /**
     * Creates a new filter widget context.
     *
     * @param string $property The name of the property being filtered.
     * @param string|null $value The current value of the filter.
     * Null indicates no filter is currently applied.
     * @param string $formId The ID of the HTML form containing the filter.
     */
    public function __construct(
        public readonly string $property,
        public readonly ?string $value,
        public readonly string $formId,
    ) {
    }
}
