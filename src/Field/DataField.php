<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Field;

use Closure;

/**
 * {@see DetailView} displayed field configuration.
 */
final class DataField
{
    /**
     * @param string $name Property name in the data object or key name in the data array. Optional if {@see value} is set explicitly.
     * @param string $label Field label. If not set, {@see name} is used.
     * @param array|Closure $labelAttributes An array of label's HTML attribute values indexed by attribute names or
     * a function accepting data and returning the array.
     * @param string $labelTag Label HTML tag.
     * @param mixed|null $value Explicit value. If `null`, the value is obtained from the data by field {@see name}.
     * @param string $valueTag Value HTML tag.
     * @param array|Closure $valueAttributes An array of value's HTML attribute values indexed by attribute names or
     * a function accepting data and returning the array.
     */
    public function __construct(
        public readonly string $name = '',
        public readonly string $label = '',
        public readonly array|Closure $labelAttributes = [],
        public readonly string $labelTag = '',
        public readonly mixed $value = null,
        public readonly string $valueTag = '',
        public readonly array|Closure $valueAttributes = []
    ) {
    }
}
