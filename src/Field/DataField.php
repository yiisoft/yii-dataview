<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Field;

use Closure;

/**
 * `DataField` represents a field configuration for {@see DetailView} widget.
 *
 * This class defines how a single field should be displayed in a {@see DetailView},
 * including its label, value, and HTML attributes for both label and value elements.
 */
final class DataField
{
    /**
     * Creates a new `DataField` instance.
     *
     * @param string $name Property name in the data object or key name in the data array.
     * Optional if `$value` is set explicitly.
     *
     * @param string $label Field label. If not set, `$name` is used.
     *
     * @param array|Closure $labelAttributes An array of label's HTML attribute values indexed by attribute names or
     * a function accepting data and returning the array.
     * Example array: `['class' => 'label', 'style' => 'color: red']`
     * Example closure: `fn($data) => ['class' => $data->type . '-label']`
     *
     * @param string $labelTag Label HTML tag.
     * Example: 'span', 'div', 'label'
     *
     * @param mixed|null $value Explicit value. If `null`, the value is obtained from the data by field `$name`.
     *
     * @param string $valueTag Value HTML tag.
     * Example: 'span', 'div', 'p'
     *
     * @param array|Closure $valueAttributes An array of value's HTML attribute values indexed by attribute names or
     * a function accepting data and returning the array.
     * Example array: `['class' => 'value', 'data-type' => 'text']`
     * Example closure: `fn($data) => ['class' => $data->status . '-value']`
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
