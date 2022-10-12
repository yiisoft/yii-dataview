<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Closure;
use InvalidArgumentException;
use JsonException;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

/**
 * DetailView displays the detail of a single data.
 *
 * DetailView is best used for displaying a data in a regular format (e.g. each data attribute is displayed using
 * flexbox).
 *
 * The data can be either object or an associative array.
 *
 * DetailView uses the {@see dataAttributes} property to determines which model dataAttributes should be displayed
 * and how they should be formatted.
 *
 * A typical usage of DetailView is as follows:
 *
 * ```php
 * <?= DetailView::widget()->data($data) ?>
 * ```
 */
final class DetailView extends Widget
{
    private array $attributes = [];
    private array $columns = [];
    private array $containerAttributes = [];
    private array|object $data = [];
    private array $dataAttributes = [];
    private string $header = '';
    private string $itemTemplate = "<div{dataAttributes}>\n{label}\n{value}\n</div>";
    private array|Closure $labelAttributes = [];
    private string $labelTag = 'span';
    private string $labelTemplate = '<{labelTag}{labelAttributes}>{label}</{labelTag}>';
    private string $template = "<div{attributes}>\n<div{containerAttributes}>\n{header}\n{items}\n</div>\n</div>";
    private array|Closure $valueAttributes = [];
    private string $valueFalse = 'false';
    private string $valueTag = 'div';
    private string $valueTemplate = '<{valueTag}{valueAttributes}>{value}</{valueTag}>';
    private string $valueTrue = 'true';

    /**
     * Returns a new instance with the HTML attributes. The following special options are recognized.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function attributes(array $values): self
    {
        $new = clone $this;
        $new->attributes = $values;

        return $new;
    }

    /**
     * Return a new instance the specified columns.
     *
     * @param array $values The grid column configuration. Each array element represents the configuration for one
     * particular grid column. For example,
     *
     * ```php
     * [
     * ]
     * ```
     */
    public function columns(array $values): self
    {
        $new = clone $this;
        $new->columns = $values;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the container items.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function containerAttributes(array $values): self
    {
        $new = clone $this;
        $new->containerAttributes = $values;

        return $new;
    }

    /**
     * Return new instance with the data.
     *
     * @param array|object $data the data model whose details are to be displayed. This can be an instance, an
     * associative array, an object.
     */
    public function data(array|object $data): self
    {
        $new = clone $this;
        $new->data = $data;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the container item.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function dataAttributes(array $values): self
    {
        $new = clone $this;
        $new->dataAttributes = $values;

        return $new;
    }

    /**
     * Return new instance with the header.
     *
     * @param string $value The header.
     */
    public function header(string $value): self
    {
        $new = clone $this;
        $new->header = $value;

        return $new;
    }

    /**
     * Return new instance with the item template.
     *
     * @param string $value The item template.
     */
    public function itemTemplate(string $value): self
    {
        $new = clone $this;
        $new->itemTemplate = $value;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the label.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function labelAttributes(array $values): self
    {
        $new = clone $this;
        $new->labelAttributes = $values;

        return $new;
    }

    /**
     * Return new instance with the label tag.
     *
     * @param string $value The tag to use for the label.
     */
    public function labelTag(string $value): self
    {
        $new = clone $this;
        $new->labelTag = $value;

        return $new;
    }

    /**
     * Return new instance with the label template.
     *
     * @param string $value The label template.
     */
    public function labelTemplate(string $value): self
    {
        $new = clone $this;
        $new->labelTemplate = $value;

        return $new;
    }

    /**
     * Return new instance with the template.
     *
     * @param string $value The template.
     */
    public function template(string $value): self
    {
        $new = clone $this;
        $new->template = $value;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the value.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function valueAttributes(array $values): self
    {
        $new = clone $this;
        $new->valueAttributes = $values;

        return $new;
    }

    /**
     * Return new instance when the value is false.
     *
     * @param string $value The value when is false.
     */
    public function valueFalse(string $value): self
    {
        $new = clone $this;
        $new->valueFalse = $value;

        return $new;
    }

    /**
     * Return new instance with the value tag.
     *
     * @param string $value The tag to use for the value.
     */
    public function valueTag(string $value): self
    {
        $new = clone $this;
        $new->valueTag = $value;

        return $new;
    }

    /**
     * Return new instance with the value template.
     *
     * @param string $value The value template.
     */
    public function valueTemplate(string $value): self
    {
        $new = clone $this;
        $new->valueTemplate = $value;

        return $new;
    }

    /**
     * Return new instance when the value is true.
     *
     * @param string $value The value when is true.
     */
    public function valueTrue(string $value): self
    {
        $new = clone $this;
        $new->valueTrue = $value;

        return $new;
    }

    /**
     * @throws JsonException
     */
    protected function run(): string
    {
        if ($this->renderItems() === '') {
            return '';
        }

        return $this->removeDoubleLinesBreaks(
            strtr(
                $this->template,
                [
                    '{attributes}' => Html::renderTagAttributes($this->attributes),
                    '{containerAttributes}' => Html::renderTagAttributes($this->containerAttributes),
                    '{dataAttributes}' => Html::renderTagAttributes($this->dataAttributes),
                    '{header}' => $this->header,
                    '{items}' => $this->renderItems(),
                ]
            )
        );
    }

    private function has(string $attribute): bool
    {
        return is_array($this->data) ? array_key_exists($attribute, $this->data) : isset($this->data->$attribute);
    }

    /**
     * @psalm-return list<
     *     array{
     *         label: string,
     *         labelAttributes: array<array-key, mixed>,
     *         labelTag: string,
     *         value: string,
     *         valueAttributes: array<array-key, mixed>,
     *         valueTag: string,
     *     }
     * >
     */
    private function normalizeColumns(array $columns): array
    {
        $normalized = [];

        /** @psalm-var array[] $columns */
        foreach ($columns as $value) {
            if (!isset($value['attribute'])) {
                throw new InvalidArgumentException('The "attribute" must be set.');
            }

            if (!is_string($value['attribute'])) {
                throw new InvalidArgumentException('The "attribute" must be a string.');
            }

            if (isset($value['label']) && !is_string($value['label'])) {
                throw new InvalidArgumentException('The "label" must be a string.');
            }

            $attribute = $value['attribute'] ?? '';
            /** @var string */
            $label = $value['label'] ?? $value['attribute'];
            /** @var array|Closure */
            $labelAttributes = $value['labelAttributes'] ?? $this->labelAttributes;
            /** @var string */
            $labelTag = $value['labelTag'] ?? $this->labelTag;
            /** @var array|Closure */
            $valueAttributes = $value['valueAttributes'] ?? $this->valueAttributes;
            /** @var string */
            $valueTag = $value['valueTag'] ?? $this->valueTag;

            $normalized[] = [
                'label' => Html::encode($label),
                'labelAttributes' => $this->renderAttributes($labelAttributes),
                'labelTag' => Html::encode($labelTag),
                'value' => Html::encodeAttribute($this->renderValue($attribute, $value['value'] ?? null)),
                'valueAttributes' => $this->renderAttributes($valueAttributes),
                'valueTag' => Html::encode($valueTag),
            ];
        }

        return $normalized;
    }

    private function renderAttributes(array|Closure $attributes): array
    {
        if ($attributes === []) {
            return [];
        }

        if ($attributes instanceof Closure) {
            return (array) $attributes($this->data);
        }

        return $attributes;
    }

    /**
     * @throws JsonException
     */
    private function renderItems(): string
    {
        $columns = $this->normalizeColumns($this->columns);

        if ($columns === []) {
            return '';
        }

        $rows = [];

        foreach ($columns as $column) {
            $label = strtr($this->labelTemplate, [
                '{label}' => $column['label'],
                '{labelTag}' => $column['labelTag'],
                '{labelAttributes}' => Html::renderTagAttributes($column['labelAttributes']),
            ]);

            $value = strtr($this->valueTemplate, [
                '{value}' => $column['value'],
                '{valueTag}' => $column['valueTag'],
                '{valueAttributes}' => Html::renderTagAttributes($column['valueAttributes']),
            ]);

            $rows[] = strtr($this->itemTemplate, [
                '{dataAttributes}' => Html::renderTagAttributes($this->dataAttributes),
                '{label}' => $label,
                '{value}' => $value,
            ]);
        }

        return implode(PHP_EOL, $rows);
    }

    private function renderValue(string $attribute, mixed $value): mixed
    {
        if ($this->data === []) {
            throw new InvalidArgumentException('The "data" must be set.');
        }

        if ($value === null && is_array($this->data) && $this->has($attribute)) {
            return match (is_bool($this->data[$attribute])) {
                true => $this->data[$attribute] ? $this->valueTrue : $this->valueFalse,
                default => $this->data[$attribute],
            };
        }

        if ($value === null && is_object($this->data) && $this->has($attribute)) {
            return match (is_bool($this->data->{$attribute})) {
                true => $this->data->{$attribute} ? $this->valueTrue : $this->valueFalse,
                default => $this->data->{$attribute},
            };
        }

        if ($value instanceof Closure) {
            return $value($this->data);
        }

        return $value;
    }

    /**
     * Remove double spaces from string.
     *
     * @param string $string String to remove double spaces from.
     */
    private function removeDoubleLinesBreaks(string $string): string
    {
        return preg_replace("/([\r\n]{4,}|[\n]{2,}|[\r]{2,})/", PHP_EOL, $string);
    }
}
