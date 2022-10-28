<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;

/**
 * DetailColumn is the default column type for the {@see DetailView} widget.
 *
 * A simple data column definition refers to an attribute in the data of the `DetailView`.
 *
 * The name of the attribute is specified by {@see attribute}.
 *
 * By setting {@see value} and {@see label}, the label and column content can be customized.
 */
final class DetailColumn
{
    private string $attribute = '';
    private string $label = '';
    private array|Closure $labelAttributes = [];
    private string $labelTag = '';
    private mixed $value = null;
    private string $valueTag = '';
    private array|Closure $valueAttributes = [];

    /**
     * Return new instance of DetailColumn with specified attribute.
     *
     * @param string $attribute The attribute name.
     */
    public function attribute(string $attribute): self
    {
        $new = clone $this;
        $new->attribute = $attribute;

        return $new;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getLabel(): string
    {
        return $this->label === '' ? $this->attribute : $this->label;
    }

    public function getLabelAttributes(): array|Closure
    {
        return $this->labelAttributes;
    }

    public function getLabelTag(): string
    {
        return $this->labelTag;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getValueAttributes(): array|Closure
    {
        return $this->valueAttributes;
    }

    public function getValueTag(): string
    {
        return $this->valueTag;
    }

    /**
     * Return new instance with the data label for the column content.
     *
     * @param string $value The data label for the column content.
     */
    public function label(string $value): self
    {
        $new = clone $this;
        $new->label = $value;

        return $new;
    }

    /**
     * Return new instance with the HTML attributes of the label column.
     *
     * @param array|Closure $values Attribute values indexed by attribute names.
     */
    public function labelAttributes(array|Closure $values): self
    {
        $new = clone $this;
        $new->labelAttributes = $values;

        return $new;
    }

    /**
     * Return new instance with the HTML tag of the label column.
     *
     * @param string $value The HTML tag of the label column.
     */
    public function labelTag(string $value): self
    {
        $new = clone $this;
        $new->labelTag = $value;

        return $new;
    }

    /**
     * Return new instance with the data value for the column content.
     *
     * @param mixed $value The data value for the column content.
     */
    public function value(mixed $value): self
    {
        $new = clone $this;
        $new->value = $value;

        return $new;
    }

    /**
     * Return new instance with the HTML attributes of the value column.
     *
     * @param array|Closure $values Attribute values indexed by attribute names.
     */
    public function valueAttributes(array|Closure $values): self
    {
        $new = clone $this;
        $new->valueAttributes = $values;

        return $new;
    }

    /**
     * Return new instance with the HTML tag of the value column.
     *
     * @param string $value The HTML tag of the value column.
     */
    public function valueTag(string $value): self
    {
        $new = clone $this;
        $new->valueTag = $value;

        return $new;
    }

    public static function create(): self
    {
        return new self();
    }
}
