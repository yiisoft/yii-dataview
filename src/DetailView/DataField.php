<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\DetailView;

use Closure;
use InvalidArgumentException;

/**
 * `DataField` represents a field configuration for {@see DetailView} widget.
 *
 * This class defines how a single field should be displayed in a {@see DetailView}
 *
 * @psalm-import-type FieldAttributesClosure from DetailView
 * @psalm-import-type LabelAttributesClosure from DetailView
 * @psalm-import-type ValueAttributesClosure from DetailView
 * @psalm-import-type GetValueClosure from DetailView
 */
final class DataField
{
    /**
     * @param string|null $property Property name in the data object or key name in the data array. Optional if `$value`
     * is set explicitly.
     * @param string|null $label Field label. If not set, `$property` or empty string is used.
     * @param bool $labelEncode Whether the label is HTML encoded
     * @param array|Closure $labelAttributes An array of label's HTML attribute values indexed by attribute names or
     * a function accepting data and returning the array.     *
     * @param mixed $value The field value. It can be:
     *  - `null` if the value should be retrieved from the data object using the property name;
     *  - a closure that will be called to get the value, format: `function (GetValueContext $context): mixed`;
     *  - other value that will be used as is.
     * @param array|Closure $valueAttributes An array of value's HTML attribute values indexed by attribute names or
     * a function accepting data and returning the array.
     * @param bool|null $valueEncode Whether the value is HTML encoded. Supported values:
     *  - `null`: stringable objects implementing {@see NoEncodeStringableInterface} aren't encoded, everything else is
     *    encoded (default behavior);
     *  - `true`: any content is encoded, regardless of type;
     *  - `false`: nothing is encoded, use with caution and only for trusted content.
     * @param array|Closure $fieldAttributes An array of label's HTML attribute values indexed by attribute names or
     *  a function accepting data and returning the array.
     * @param bool $visible Whether the field is visible.
     *
     * @psalm-param array|LabelAttributesClosure $labelAttributes
     * @psalm-param GetValueClosure|mixed $value
     * @psalm-param array|ValueAttributesClosure $valueAttributes
     * @psalm-param array|FieldAttributesClosure $fieldAttributes
     */
    public function __construct(
        public readonly ?string $property = null,
        public readonly ?string $label = null,
        public readonly bool $labelEncode = true,
        public readonly array|Closure $labelAttributes = [],
        public readonly mixed $value = null,
        public readonly ?bool $valueEncode = null,
        public readonly array|Closure $valueAttributes = [],
        public readonly array|Closure $fieldAttributes = [],
        public readonly bool $visible = true,
    ) {
        if ($property === null && $value === null) {
            throw new InvalidArgumentException('Either "property" or "value" must be set.');
        }
    }
}
