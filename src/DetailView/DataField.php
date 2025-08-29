<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\DetailView;

use Closure;
use InvalidArgumentException;
use Stringable;

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
     * @param string $property Property name in the data object or key name in the data array.
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
     * @param Closure|float|int|string|Stringable|null $value The field value. It can be:
     *  - `null` if the value should be retrieved from the data object using the property name;
     *  - a closure that will be called to get the value, format: `function (array|object $data): string`;
     *  - string, `Stringable`, integer or float which will be used as is.
     *
     * @param string $valueTag Value HTML tag.
     * Example: 'span', 'div', 'p'
     *
     * @param array|Closure $valueAttributes An array of value's HTML attribute values indexed by attribute names or
     * a function accepting data and returning the array.
     * Example array: `['class' => 'value', 'data-type' => 'text']`
     * Example closure: `fn($data) => ['class' => $data->status . '-value']`
     *
     * @param bool $encodeValue Whether the value is HTML encoded
     *
     * @template TData as array|object
     * @psalm-param string|Stringable|int|float|(Closure(TData): string)|null $value
     */
    public function __construct(
        public readonly string $property = '',
        public readonly string $label = '',
        public readonly array|Closure $labelAttributes = [],
        public readonly string $labelTag = '',
        public readonly string|Stringable|int|float|Closure|null $value = null,
        public readonly string $valueTag = '',
        public readonly array|Closure $valueAttributes = [],
        public readonly bool $encodeValue = true,
    ) {
        if ($label === '' && $property === '') {
            throw new InvalidArgumentException('Either DataField "property" or "label" must be set.');
        }
    }
}
