<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Closure;
use InvalidArgumentException;
use JsonException;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\DataView\Field\DataField;

/**
 * DetailView displays the detail of a single data.
 *
 * DetailView is best used for displaying a data in a regular format (e.g. each field is displayed using flexbox).
 *
 * The data can be either object or an associative array.
 *
 * DetailView uses the {@see data} property to determines which model should be displayed how they should be formatted.
 *
 * A typical usage of DetailView is as follows:
 *
 * ```php
 * <?= DetailView::widget()
 *     ->data(['id' => 1, 'username' => 'tests 1', 'status' => true])
 *     ->fields(
 *         DataField::create()->attribute('id'),
 *         DataField::create()->attribute('username'),
 *         DataField::create()->attribute('status'),
 *     )
 *     ->render()
 * ```
 */
final class DetailView extends Widget
{
    private array $attributes = [];
    private array $containerAttributes = [];
    private array|object $data = [];
    private array $dataAttributes = [];
    private array $fields = [];
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
     * Return a new instance the specified fields.
     *
     * @param DataField ...$value The `DetailView` column configuration. Each object represents the configuration for
     * one particular DetailView column. For example,
     *
     * ```php
     * [
     *    DataField::create()->label('Name')->value($data->name),
     * ]
     * ```
     */
    public function fields(DataField ...$value): self
    {
        $new = clone $this;
        $new->fields = $value;

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
    public function render(): string
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
    private function normalizeColumns(array $fields): array
    {
        $normalized = [];

        /** @psalm-var DataField[] $fields */
        foreach ($fields as $field) {
            if ($field->getLabel() === '') {
                throw new InvalidArgumentException('The "attribute" or "label" must be set.');
            }

            $labelAttributes = $field->getLabelAttributes() === []
                ? $this->labelAttributes : $field->getLabelAttributes();
            $labelTag = $field->getLabelTag() === '' ? $this->labelTag : $field->getLabelTag();
            $valueTag = $field->getValueTag() === '' ? $this->valueTag : $field->getValueTag();
            $valueAttributes = $field->getValueAttributes() === []
                ? $this->valueAttributes : $field->getValueAttributes();

            $normalized[] = [
                'label' => Html::encode($field->getLabel()),
                'labelAttributes' => $this->renderAttributes($labelAttributes),
                'labelTag' => Html::encode($labelTag),
                'value' => Html::encodeAttribute($this->renderValue($field->getAttribute(), $field->getValue())),
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
        $fields = $this->normalizeColumns($this->fields);

        if ($fields === []) {
            return '';
        }

        $rows = [];

        foreach ($fields as $field) {
            $label = strtr($this->labelTemplate, [
                '{label}' => $field['label'],
                '{labelTag}' => $field['labelTag'],
                '{labelAttributes}' => Html::renderTagAttributes($field['labelAttributes']),
            ]);

            $value = strtr($this->valueTemplate, [
                '{value}' => $field['value'],
                '{valueTag}' => $field['valueTag'],
                '{valueAttributes}' => Html::renderTagAttributes($field['valueAttributes']),
            ]);

            $rows[] = strtr($this->itemTemplate, [
                '{dataAttributes}' => Html::renderTagAttributes($this->dataAttributes),
                '{label}' => $label,
                '{value}' => $value,
            ]);
        }

        return implode("\n", $rows);
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
        return preg_replace("/([\r\n]{4,}|[\n]{2,}|[\r]{2,})/", "\n", $string);
    }
}
