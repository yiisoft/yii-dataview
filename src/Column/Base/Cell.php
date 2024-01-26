<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Stringable;
use Yiisoft\Html\Html;
use Yiisoft\Html\NoEncodeStringableInterface;

final class Cell
{
    private bool $doubleEncode = true;

    public function __construct(
        private array $attributes = [],
        private ?bool $encode = null,
        private string|Stringable $content = '',
    ) {
    }

    /**
     * @param bool|null $encode Whether to encode tag content. Supported values:
     *  - `null`: stringable objects that implement interface {@see NoEncodeStringableInterface} are not encoded,
     *    everything else is encoded;
     *  - `true`: any content is encoded;
     *  - `false`: nothing is encoded.
     * Defaults to `null`.
     */
    public function encode(?bool $encode): self
    {
        $new = clone $this;
        $new->encode = $encode;
        return $new;
    }

    /**
     * @param bool $doubleEncode Whether already encoded HTML entities in tag content should be encoded.
     * Defaults to `true`.
     */
    public function doubleEncode(bool $doubleEncode): self
    {
        $new = clone $this;
        $new->doubleEncode = $doubleEncode;
        return $new;
    }

    /**
     * @param string|Stringable $content Tag content.
     */
    public function content(string|Stringable $content): self
    {
        $new = clone $this;
        $new->content = $content;
        return $new;
    }

    /**
     * Add a set of attributes to existing cell attributes.
     * Same named attributes are replaced.
     *
     * @param array $attributes Name-value set of attributes.
     */
    public function addAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->attributes = array_merge($new->attributes, $attributes);
        return $new;
    }

    /**
     * Replace attributes with a new set.
     *
     * @param array $attributes Name-value set of attributes.
     */
    public function attributes(array $attributes): self
    {
        $new = clone $this;
        $new->attributes = $attributes;
        return $new;
    }

    /**
     * Set attribute value.
     *
     * @param string $name Name of the attribute.
     * @param mixed $value Value of the attribute.
     */
    public function attribute(string $name, mixed $value): self
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    public function addClass(?string $class): self
    {
        $new = clone $this;
        Html::addCssClass($new->attributes, $class);
        return $new;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function isEncode(): ?bool
    {
        return $this->encode;
    }

    public function isDoubleEncode(): bool
    {
        return $this->doubleEncode;
    }

    public function getContent(): string|Stringable
    {
        return $this->content;
    }
}
