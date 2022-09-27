<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Closure;
use Stringable;
use RuntimeException;
use InvalidArgumentException;
use Yiisoft\Yii\DataView\Exception\InvalidConfigException;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Html\Html;

final class DataAttribute
{
    private string $name = '';
    private ?string $label = null;
    /** @var Closure|string|null */
    private $format;
    private bool $encode = false;
    /** @var Closure|string|Stringable|null */
    private $value;

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function name(string $name): self
    {
        if (!preg_match('/^(?P<name>[^:]+)(:(?P<format>\w*))?(:(?P<label>.*))?$/', $name, $matches)) {
            throw new InvalidConfigException(
                'The attribute must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"'
            );
        }

        $this->name = $matches['name'];

        if (isset($matches['format'])) {
            $this->format('{0,' . $matches['format'] . '}');
        }

        if (isset($matches['label'])) {
            $this->label($matches['label']);
        }

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function label(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function encode(bool $encode = true): self
    {
        $this->encode = $encode;

        return $this;
    }

    /**
     * @param Closure|string|null $format
     *
     * @throws InvalidArgumentException
     */
    public function format($format): self
    {
        if ($format !== null && !is_string($format) && !is_a($format, Closure::class)) {
            throw new InvalidArgumentException('Format must be type of "null", "string" or Closure instance');
        }
        /** @var Closure|string|null $format */
        $this->format = $format;

        return $this;
    }

    /**
     * @param Closure|string|Stringable|null $value
     *
     * @throws InvalidArgumentException
     */
    public function value($value): self
    {
        if ($value !== null && !is_string($value) && !is_a($value, Closure::class) && !is_a($value, Stringable::class)) {
            throw new InvalidArgumentException('Value must be type of "null", "string" or Closure instance');
        }
        /** @var Closure|string|Stringable|null $value */
        $this->value = $value;

        return $this;
    }

    /**
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     *
     */
    public function getValue(mixed $model): string
    {
        if (!is_array($model) && !is_object($model)) {
            throw new InvalidArgumentException('Model must be type of "array" or "object"');
        }

        $value = null;

        if (is_string($this->value)) {
            /** @var mixed */
            $value = ArrayHelper::getValueByPath($model, $this->value);
        } elseif ($this->value instanceof Stringable) {
            $value = (string) $this->value;
        } elseif ($this->value instanceof Closure) {
            /** @var mixed */
            $value = ($this->value)(...func_get_args());
        } elseif ($this->name) {
            /** @var mixed */
            $value = ArrayHelper::getValueByPath($model, $this->name);
        }

        if (empty($value)) {
            return '';
        }

        if (is_string($this->format)) {
            $value = $this->translator->translate($this->format, (array) $value);
        } elseif ($this->format instanceof Closure) {
            /** @var mixed */
            $value = ($this->format)($value);
        }

        return $this->encode ? Html::encode($value) : (string) $value;
    }
}
