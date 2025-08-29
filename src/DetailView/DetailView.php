<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\DetailView;

use Closure;
use InvalidArgumentException;
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
 *
 * @psalm-type FieldAttributesClosure = Closure(FieldContext): array
 * @psalm-type LabelAttributesClosure = Closure(LabelContext): array
 * @psalm-type ValueAttributesClosure = Closure(ValueContext): array
 */
final class DetailView extends Widget
{
    private array|object $data = [];

    /** @psalm-var list<DataField> */
    private array $fields = [];

    /** @psalm-var non-empty-string|null */
    private ?string $containerTag = null;
    private array $containerAttributes = [];
    private string $prepend = '';
    private string $append = '';

    /** @psalm-var non-empty-string|null */
    private ?string $listTag = 'dl';
    private array $listAttributes = [];

    /** @psalm-var non-empty-string|null */
    private ?string $fieldTag = null;
    /** @psalm-var array|FieldAttributesClosure */
    private array|Closure $fieldAttributes = [];
    private string $fieldPrepend = '';
    private string $fieldAppend = '';
    private string $fieldTemplate = "{label}\n{value}";

    /** @psalm-var non-empty-string|null */
    private ?string $labelTag = 'dt';
    /** @psalm-var array|LabelAttributesClosure */
    private array|Closure $labelAttributes = [];
    private string $labelPrepend = '';
    private string $labelAppend = '';

    private array|Closure $valueAttributes = [];
    private string $valueFalse = 'false';
    private string $valueTag = 'dd';
    private string $valueTemplate = '<{tag}{attributes}>{value}</{tag}>';
    private string $valueTrue = 'true';

    public function containerTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->containerTag = $tag;
        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for container.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    public function containerAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->containerAttributes = $attributes;
        return $new;
    }

    /**
     * Returns a new instance with HTML content to be added after the opening container tag.
     *
     * @param string|Stringable ...$prepend The HTML content to be prepended.
     */
    public function prepend(string|Stringable ...$prepend): self
    {
        $new = clone $this;
        $new->prepend = implode('', $prepend);
        return $new;
    }

    /**
     * Returns a new instance with HTML content to be added before the closing container tag.
     *
     * @param string|Stringable ...$append The HTML content to be appended.
     */
    public function append(string|Stringable ...$append): self
    {
        $new = clone $this;
        $new->append = implode('', $append);
        return $new;
    }

    public function listTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->listTag = $tag;
        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the list set.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     */
    public function listAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->listAttributes = $attributes;
        return $new;
    }

    public function fieldTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->fieldTag = $tag;
        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the field container tag set.
     *
     * @param array|Closure $attributes Attribute values indexed by attribute names.
     *
     * @psalm-param array|FieldAttributesClosure $attributes
     */
    public function fieldAttributes(array|Closure $attributes): self
    {
        $new = clone $this;
        $new->fieldAttributes = $attributes;
        return $new;
    }

    public function fieldPrepend(string|Stringable ...$prepend): self
    {
        $new = clone $this;
        $new->fieldPrepend = implode('', $prepend);
        return $new;
    }

    public function fieldAppend(string|Stringable ...$append): self
    {
        $new = clone $this;
        $new->fieldAppend = implode('', $append);
        return $new;
    }

    /**
     * Return new instance with the field template set.
     *
     * Available placeholders are `{label}` and `{value}`.
     *
     * @param string $template The field template.
     */
    public function fieldTemplate(string $template): self
    {
        $new = clone $this;
        $new->fieldTemplate = $template;
        return $new;
    }

    public function labelTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->labelTag = $tag;
        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the field label.
     *
     * @param array|Closure $attributes Attribute values indexed by attribute names.
     *
     * @psalm-param array|FieldAttributesClosure $attributes
     */
    public function labelAttributes(array|Closure $attributes): self
    {
        $new = clone $this;
        $new->labelAttributes = $attributes;
        return $new;
    }

    public function labelPrepend(string|Stringable ...$prepend): self
    {
        $new = clone $this;
        $new->labelPrepend = implode('', $prepend);
        return $new;
    }

    public function labelAppend(string|Stringable ...$append): self
    {
        $new = clone $this;
        $new->labelAppend = implode('', $append);
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

    public function render(): string
    {
        $content = $this->renderList();
        if ($content === '') {
            return '';
        }

        if ($this->prepend !== '') {
            $content = $this->prepend . "\n" . $content;
        }
        if ($this->append !== '') {
            $content .= "\n" . $this->append;
        }

        return $this->containerTag === null
            ? $content
            : Html::tag($this->containerTag, "\n" . $content . "\n", $this->containerAttributes)
                ->encode(false)
                ->render();
    }

    private function renderList(): string
    {
        $content = $this->renderFields();
        if ($content === '') {
            return '';
        }

        return $this->listTag === null
            ? $content
            : Html::tag($this->listTag, "\n" . $content . "\n", $this->listAttributes)
                ->encode(false)
                ->render();
    }

    private function renderFields(): string
    {
        return implode(
            "\n",
            array_map(
                $this->renderField(...),
                $this->fields,
            ),
        );
    }

    private function renderField(DataField $field): string
    {
        $context = new FieldContext(
            $field,
            $this->data,
            $this->renderValue($field),
            $this->renderLabel($field),
        );

        $content = strtr(
            $this->fieldTemplate,
            [
                '{label}' => $context->label,
                '{value}' => $context->value,
            ],
        );

        if ($this->fieldPrepend !== '') {
            $content = $this->fieldPrepend . "\n" . $content;
        }
        if ($this->fieldAppend !== '') {
            $content .= "\n" . $this->fieldAppend;
        }

        if ($this->fieldTag === null) {
            return $content;
        }

        $attributes = $this->fieldAttributes instanceof Closure
            ? ($this->fieldAttributes)($context)
            : $this->fieldAttributes;

        return Html::tag($this->fieldTag, "\n" . $content . "\n", $attributes)
            ->encode(false)
            ->render();
    }

    private function renderLabel(DataField $field): string
    {
        $label = $field->label === '' ? $field->property : $field->label;
        if ($label === '') {
            return '';
        }

        $content = Html::encode($label);

        if ($this->labelPrepend !== '') {
            $content = $this->labelPrepend . "\n" . $content;
        }
        if ($this->labelAppend !== '') {
            $content .= "\n" . $this->labelAppend;
        }

        if ($this->labelTag === null) {
            return $content;
        }

        $context = new LabelContext($field, $this->data);
        $attributes = $this->labelAttributes instanceof Closure
            ? ($this->labelAttributes)($context)
            : $this->labelAttributes;

        return Html::tag($this->labelTag, $content, $attributes)
            ->encode(false)
            ->render();

        return strtr(
            $this->labelTemplate,
            [
                '{label}' => Html::encode($field->label === '' ? $field->property : $field->label),
                '{tag}' => $field->labelTag === '' ? $this->labelTag : $field->labelTag,
                '{attributes}' => Html::renderTagAttributes(
                    $this->prepareFieldAttributes(
                        $field->labelAttributes === [] ? $this->labelAttributes : $field->labelAttributes,
                    )
                ),
            ]
        );
    }

    private function renderValue(DataField $field): string
    {
        return strtr(
            $this->valueTemplate,
            [
                '{value}' => $field->encodeValue
                    ? Html::encodeAttribute($this->renderValueInternal($field->property, $field->value))
                    : $this->renderValueInternal($field->property, $field->value),
                '{tag}' => $field->valueTag === '' ? $this->valueTag : $field->valueTag,
                '{attributes}' => Html::renderTagAttributes(
                    $this->prepareFieldAttributes(
                        $field->valueAttributes === [] ? $this->valueAttributes : $field->valueAttributes,
                    )
                ),
            ],
        );
    }

    private function renderValueInternal(string $property, string|Stringable|int|float|Closure|null $value): string
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
}
