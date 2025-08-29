<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\DetailView;

use Closure;
use InvalidArgumentException;
use JsonException;
use Stringable;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

use function array_key_exists;
use function is_array;
use function is_bool;

/**
 * `DetailView` displays details about a single data item.
 * The data can be either an object or an associative array.
 * Which fields should be displayed and how exactly is determined
 * by "fields":
 *
 * ```
 * <?= DetailView::widget()
 *     ->data(['id' => 1, 'username' => 'tests 1', 'status' => true])
 *     ->fields(
 *         new DataField('id'),
 *         new DataField('username'),
 *         new DataField('status'),
 *     )
 * ?>
 * ```
 */
final class DetailView extends Widget
{
    private array $attributes = [];
    private array $fieldListAttributes = [];
    private array|object $data = [];
    private array $fieldAttributes = [];
    /**
     * @psalm-var list<DataField>
     */
    private array $fields = [];
    private string $header = '';
    private string $fieldTemplate = "<div{attributes}>\n{label}\n{value}\n</div>";
    private array|Closure $labelAttributes = [];
    private string $labelTag = 'dt';
    private string $labelTemplate = '<{tag}{attributes}>{label}</{tag}>';
    private string $template = "<div{attributes}>\n{header}\n<dl{fieldListAttributes}>\n{fields}\n</dl>\n</div>";
    private array|Closure $valueAttributes = [];
    private string $valueFalse = 'false';
    private string $valueTag = 'dd';
    private string $valueTemplate = '<{tag}{attributes}>{value}</{tag}>';
    private string $valueTrue = 'true';

    /**
     * Returns a new instance with the main widget tag HTML attributes set.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    public function attributes(array $attributes): self
    {
        $new = clone $this;
        $new->attributes = $attributes;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the field list set.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    public function fieldListAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->fieldListAttributes = $attributes;

        return $new;
    }

    /**
     * Return new instance with the data.
     *
     * @param array|object $data The data model whose details are to be displayed.
     * This can be an object or an associative array.
     */
    public function data(array|object $data): self
    {
        $new = clone $this;
        $new->data = $data;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the field container tag set.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    public function fieldAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->fieldAttributes = $attributes;

        return $new;
    }

    /**
     * Return a new instance with the specified fields configuration.
     *
     * @param DataField ...$fields The field configurations. Each object represents the configuration for
     * one particular field. For example,
     *
     * ```php
     * [
     *    DataField::create()->label('Name')->value($data->name),
     * ]
     * ```
     *
     * @no-named-arguments
     */
    public function fields(DataField ...$fields): self
    {
        $new = clone $this;
        $new->fields = $fields;

        return $new;
    }

    /**
     * Return new instance with the header content set.
     * Note that header content is not HTML-encoded.
     *
     * @param string $content The header content.
     */
    public function header(string $content): self
    {
        $new = clone $this;
        $new->header = $content;

        return $new;
    }

    /**
     * Return new instance with the field template set.
     *
     * Available placeholders are `{attributes}`, `{label}`, and `{value}`.
     *
     * @param string $template The field template.
     */
    public function fieldTemplate(string $template): self
    {
        $new = clone $this;
        $new->fieldTemplate = $template;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the field label.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    public function labelAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->labelAttributes = $attributes;

        return $new;
    }

    /**
     * Return new instance with the HTML tag to use for the label.
     *
     * @param string $tag The HTML tag name.
     */
    public function labelTag(string $tag): self
    {
        $new = clone $this;
        $new->labelTag = $tag;

        return $new;
    }

    /**
     * Return new instance with the label template.
     *
     * Available placeholders are `{attributes}`, `{label}`, and `{tag}`.
     *
     * @param string $template The label template.
     */
    public function labelTemplate(string $template): self
    {
        $new = clone $this;
        $new->labelTemplate = $template;

        return $new;
    }

    /**
     * Return new instance with the overall widget template set.
     *
     * Available placeholders are `{attributes}`, `{header}`, `{fieldListAttributes}`, and `{fields}`.
     *
     * @param string $template The template.
     */
    public function template(string $template): self
    {
        $new = clone $this;
        $new->template = $template;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the value.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    public function valueAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->valueAttributes = $attributes;

        return $new;
    }

    /**
     * Return new instance with the content displayed when the value is `false`.
     *
     * @param string $content The content.
     */
    public function valueFalse(string $content): self
    {
        $new = clone $this;
        $new->valueFalse = $content;

        return $new;
    }

    /**
     * Return new instance with the value tag.
     *
     * @param string $tag HTML tag.
     */
    public function valueTag(string $tag): self
    {
        $new = clone $this;
        $new->valueTag = $tag;

        return $new;
    }

    /**
     * Return new instance with the value template.
     *
     * Available placeholders are `{attributes}`, `{value}`, and `{tag}`.
     *
     * @param string $template The value template.
     */
    public function valueTemplate(string $template): self
    {
        $new = clone $this;
        $new->valueTemplate = $template;

        return $new;
    }

    /**
     * Return new instance with the content displayed when the value is `true`.
     *
     * @param string $content The content.
     */
    public function valueTrue(string $content): self
    {
        $new = clone $this;
        $new->valueTrue = $content;

        return $new;
    }

    /**
     * @throws JsonException
     */
    public function render(): string
    {
        if ($this->renderFields() === '') {
            return '';
        }

        return $this->removeDoubleLineBreaks(
            strtr(
                $this->template,
                [
                    '{attributes}' => Html::renderTagAttributes($this->attributes),
                    '{header}' => $this->header,
                    '{fieldListAttributes}' => Html::renderTagAttributes($this->fieldListAttributes),
                    '{fields}' => $this->renderFields(),
                ]
            )
        );
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
    private function renderFields(): string
    {
        if ($this->fields === []) {
            return '';
        }

        $rows = [];

        foreach ($this->fields as $field) {
            $label = strtr($this->labelTemplate, [
                '{label}' => Html::encode($field->label === '' ? $field->property : $field->label),
                '{tag}' => $field->labelTag === '' ? $this->labelTag : $field->labelTag,
                '{attributes}' => Html::renderTagAttributes(
                    $this->renderAttributes(
                        $field->labelAttributes === [] ? $this->labelAttributes : $field->labelAttributes,
                    )
                ),
            ]);

            $value = strtr($this->valueTemplate, [
                '{value}' => $field->encodeValue
                    ? Html::encodeAttribute($this->renderValue($field->property, $field->value))
                    : $this->renderValue($field->property, $field->value),
                '{tag}' => $field->valueTag === '' ? $this->valueTag : $field->valueTag,
                '{attributes}' => Html::renderTagAttributes(
                    $this->renderAttributes(
                        $field->valueAttributes === [] ? $this->valueAttributes : $field->valueAttributes,
                    )
                ),
            ]);

            $rows[] = strtr($this->fieldTemplate, [
                '{attributes}' => Html::renderTagAttributes($this->fieldAttributes),
                '{label}' => $label,
                '{value}' => $value,
            ]);
        }

        return implode("\n", $rows);
    }

    private function renderValue(string $property, string|Stringable|int|float|Closure|null $value): string
    {
        if ($this->data === []) {
            throw new InvalidArgumentException('The "data" must be set.');
        }

        if ($value === null) {
            return $this->extractValueFromData($property);
        }

        if ($value instanceof Closure) {
            /**
             * @psalm-var Closure(array|object): string $value
             */
            return $value($this->data);
        }

        return (string) $value;
    }

    private function extractValueFromData(string $property): string
    {
        if (is_array($this->data)) {
            if (array_key_exists($property, $this->data)) {
                return (string) match (is_bool($this->data[$property])) {
                    true => $this->data[$property] ? $this->valueTrue : $this->valueFalse,
                    default => $this->data[$property],
                };
            }
            return '';
        }

        if (isset($this->data->$property)) {
            return (string) match (is_bool($this->data->{$property})) {
                true => $this->data->{$property} ? $this->valueTrue : $this->valueFalse,
                default => $this->data->{$property},
            };
        }

        return '';
    }

    /**
     * Remove double line breaks from a string.
     *
     * @param string $string The valid UTF-8 string to remove double line breaks from.
     */
    private function removeDoubleLineBreaks(string $string): string
    {
        /**
         * @var string We assume that `$string` is a valid UTF-8 string.
         */
        return preg_replace('/(\R{2,})/', "\n", $string);
    }
}
