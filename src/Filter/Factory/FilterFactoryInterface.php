<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Factory;

use Yiisoft\Data\Reader\FilterInterface;

/**
 * Interface for creating data reader filters.
 *
 * Filter factories are responsible for creating filter objects that define
 * how data should be filtered based on property values. Different implementations
 * of this interface can create different types of filters (equals, like, range, etc.).
 *
 * @see FilterInterface The interface implemented by all filter objects
 */
interface FilterFactoryInterface
{
    /**
     * Creates a filter for the specified property and value.
     *
     * @param string $property The property name to filter on.
     * @param string $value The value to filter by.
     *
     * @throws IncorrectValueException When the provided value is not valid for the filter type.
     * @return FilterInterface The created filter.
     */
    public function create(string $property, string $value): FilterInterface;
}
