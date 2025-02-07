<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Factory;

use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\FilterInterface;

/**
 * Factory for creating equality filters that match exact values.
 *
 * This factory creates filters that perform strict equality comparison between
 * a property value and a specified value. It's typically used for filtering
 * data where an exact match is required, such as IDs or status values.
 */
final class EqualsFilterFactory implements FilterFactoryInterface
{
    /**
     * Creates an equals filter for the specified property and value.
     *
     * @param string $property The property name to filter on.
     * @param string $value The value to match exactly.
     *
     * @return FilterInterface|null The equals filter, or null if the value is empty.
     *
     * @see Equals The filter class used for equality comparison
     */
    public function create(string $property, string $value): ?FilterInterface
    {
        if (empty($value)) {
            return null;
        }

        return new Equals($property, $value);
    }
}
