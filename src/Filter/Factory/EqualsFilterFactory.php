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
    public function create(string $property, string $value): ?FilterInterface
    {
        if (empty($value)) {
            return null;
        }

        return new Equals($property, $value);
    }
}
