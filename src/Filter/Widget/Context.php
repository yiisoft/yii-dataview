<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Widget;

/**
 * Context for filter widgets that provides necessary data for rendering filter inputs.
 *
 * This class encapsulates the data required by filter widgets to render their inputs
 * and maintain their state. It includes:
 * - The property being filtered
 * - The current filter value
 * - The ID of the form containing the filter
 */
final class Context
{
    /**
     * Creates a new filter widget context.
     *
     * @param string $property The name of the property being filtered.
     * This typically corresponds to a column name or data field.
     *
     * @param string|null $value The current value of the filter.
     * Null indicates no filter is currently applied.
     *
     * @param string $formId The ID of the HTML form containing the filter.
     * This is used to properly scope the filter inputs and handle form submissions.
     */
    public function __construct(
        public readonly string $property,
        public readonly ?string $value,
        public readonly string $formId,
    ) {
    }
}
