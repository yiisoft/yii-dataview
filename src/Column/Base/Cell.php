<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Stringable;
use Yiisoft\Html\Html;
use Yiisoft\Html\NoEncodeStringableInterface;

/**
 * Cell represents a single grid cell with content and attributes.
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
     * @param array $attributes HTML attributes for the cell.
     * @param bool|null $encode Whether to encode content. See {@see encode()} for details.
     * @param string|Stringable ...$content Cell content items.
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
     * @param bool|null $encode Whether to encode tag content. Supported values:
     *  - `null`: stringable objects implementing {@see NoEncodeStringableInterface} aren't encoded,
     *    everything else is encoded
     *  - `true`: any content is encoded
     *  - `false`: nothing is encoded
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
     * @param bool $doubleEncode Whether to double encode HTML entities.
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
     * @param string|Stringable ...$content Tag content.
     */
    public function content(string|Stringable ...$content): self
    {
        $new = clone $this;
        $new->content = $content;
        return $new;
    }

    /**
     * Add attributes to existing cell attributes.
     * Attributes with the same name are replaced.
     *
     * @param array $attributes HTML attributes as name-value pairs.
     *
     * @return self New instance with merged attributes
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
     * @return array HTML attributes as name-value pairs
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get content encoding setting.
     *
     * @return bool|null Current encoding setting.
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
     * @psalm-return array<array-key,string|Stringable>
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * Check if cell content is empty.
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
