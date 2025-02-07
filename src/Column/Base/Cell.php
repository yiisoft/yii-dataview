<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Stringable;
use Yiisoft\Html\Html;
use Yiisoft\Html\NoEncodeStringableInterface;

/**
 * Cell represents a single grid cell with content and attributes.
 *
 * This immutable class provides a fluent interface for configuring grid cells in data views.
 * It handles:
 * - Cell content management with encoding options
 * - HTML attribute manipulation
 * - CSS class management
 * - Content emptiness checking
 *
 * Features:
 * - Immutable design with fluent interface
 * - Flexible content encoding options
 * - Support for HTML attributes
 * - CSS class manipulation
 * - Content validation
 *
 * Example usage:
 * ```php
 * // Basic cell with content
 * $cell = new Cell(content: 'Hello World');
 *
 * // Cell with HTML attributes and classes
 * $cell = (new Cell())
 *     ->content('Total: $100')
 *     ->attributes(['class' => 'total-cell'])
 *     ->addClass('highlighted');
 *
 * // Cell with HTML content and encoding control
 * $cell = (new Cell())
 *     ->content('<strong>Important</strong>')
 *     ->encode(false)
 *     ->addClass('warning');
 *
 * // Cell with multiple content items
 * $cell = (new Cell())
 *     ->content('Price: ', '$', '100')
 *     ->addClass('price-cell');
 * ```
 */
final class Cell
{
    /**
     * @var bool Whether to double encode HTML entities in content.
     */
    private bool $doubleEncode = true;

    /**
     * @psalm-var array<array-key,string|Stringable>
     */
    private array $content;

    /**
     * Creates a new cell instance.
     *
     * @param array $attributes HTML attributes for the cell as name-value pairs.
     * @param bool|null $encode Whether to encode content. See {@see encode()} for details.
     * @param string|Stringable ...$content Cell content items. Multiple items can be provided
     *                                     and will be concatenated when rendered.
     */
    public function __construct(
        private array $attributes = [],
        private ?bool $encode = null,
        string|Stringable ...$content,
    ) {
        $this->content = $content;
    }

    /**
     * Set content encoding behavior.
     *
     * This method controls how the cell content is encoded to prevent XSS attacks
     * while allowing intentional HTML content when needed.
     *
     * @param bool|null $encode Whether to encode tag content. Supported values:
     *  - `null`: stringable objects implementing {@see NoEncodeStringableInterface} aren't encoded,
     *    everything else is encoded (default behavior)
     *  - `true`: any content is encoded, regardless of type
     *  - `false`: nothing is encoded, use with caution and only for trusted content
     *
     * @return self New instance with updated encoding setting.
     */
    public function encode(?bool $encode): self
    {
        $new = clone $this;
        $new->encode = $encode;
        return $new;
    }

    /**
     * Set whether to double-encode HTML entities in content.
     *
     * This is useful when the content might already contain encoded entities
     * and you need to control whether they should be encoded again.
     *
     * @param bool $doubleEncode Whether to double encode HTML entities.
     *                          Set to false if content already contains encoded entities.
     *
     * @return self New instance with updated double encode setting.
     */
    public function doubleEncode(bool $doubleEncode): self
    {
        $new = clone $this;
        $new->doubleEncode = $doubleEncode;
        return $new;
    }

    /**
     * Set the cell content.
     *
     * Multiple content items can be provided and will be concatenated when rendered.
     * Each item can be either a string or an object implementing Stringable.
     *
     * @param string|Stringable ...$content Cell content items.
     *
     * @return self New instance with updated content.
     */
    public function content(string|Stringable ...$content): self
    {
        $new = clone $this;
        $new->content = $content;
        return $new;
    }

    /**
     * Add attributes to existing cell attributes.
     *
     * This method merges new attributes with existing ones. If an attribute
     * already exists, it will be replaced with the new value.
     *
     * @param array $attributes HTML attributes as name-value pairs.
     *
     * @return self New instance with merged attributes.
     */
    public function addAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->attributes = array_merge($new->attributes, $attributes);
        return $new;
    }

    /**
     * Replace all attributes with a new set.
     *
     * Unlike addAttributes(), this method completely replaces all existing
     * attributes with the new set.
     *
     * @param array $attributes HTML attributes as name-value pairs.
     *
     * @return self New instance with replaced attributes.
     */
    public function attributes(array $attributes): self
    {
        $new = clone $this;
        $new->attributes = $attributes;
        return $new;
    }

    /**
     * Set a single attribute value.
     *
     * This is a convenience method for setting a single attribute without
     * affecting other existing attributes.
     *
     * @param string $name Attribute name.
     * @param mixed $value Attribute value.
     *
     * @return self New instance with updated attribute.
     */
    public function attribute(string $name, mixed $value): self
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    /**
     * Add a CSS class to the cell.
     *
     * This method safely adds a CSS class to the cell's class attribute,
     * maintaining any existing classes.
     *
     * @param string|null $class CSS class name to add.
     *
     * @return self New instance with added CSS class.
     */
    public function addClass(?string $class): self
    {
        $new = clone $this;
        Html::addCssClass($new->attributes, $class);
        return $new;
    }

    /**
     * Get cell HTML attributes.
     *
     * @return array HTML attributes as name-value pairs.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get content encoding setting.
     *
     * @return bool|null Current encoding setting:
     *                   - null: default encoding behavior
     *                   - true: force encoding
     *                   - false: disable encoding
     */
    public function isEncode(): ?bool
    {
        return $this->encode;
    }

    /**
     * Get double encode setting.
     *
     * @return bool Whether HTML entities in content should be double encoded.
     */
    public function isDoubleEncode(): bool
    {
        return $this->doubleEncode;
    }

    /**
     * Get cell content items.
     *
     * @return array<array-key,string|Stringable> Array of content items.
     *
     * @psalm-return array<array-key,string|Stringable>
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * Check if cell content is empty.
     *
     * A cell is considered empty if all its content items are empty strings
     * when converted to string representation.
     *
     * @return bool Whether all content items are empty strings.
     */
    public function isEmptyContent(): bool
    {
        foreach ($this->content as $content) {
            if (!empty((string) $content)) {
                return false;
            }
        }
        return true;
    }
}
