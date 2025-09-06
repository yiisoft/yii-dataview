<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\DetailView;

use Closure;
use InvalidArgumentException;
use Stringable;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\DataView\ValuePresenter\SimpleValuePresenter;
use Yiisoft\Yii\DataView\ValuePresenter\ValuePresenterInterface;

/**
 * `DetailView` displays details about a single data item. The data can be either an object or an associative array.
 * Which fields should be displayed and how exactly is determined by "fields":
 *
 * ```php
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
 * @psalm-type GetValueClosure = Closure(GetValueContext): mixed
 */
final class DetailView extends Widget
{
    private array|object $data = [];

    /** @psalm-var list<DataField> */
    private array $fields = [];

    /** @psalm-var non-empty-string|null */
    private string|null $containerTag = null;
    private array $containerAttributes = [];
    private string $prepend = '';
    private string $append = '';

    /** @psalm-var non-empty-string|null */
    private ?string $listTag = 'dl';
    private array $listAttributes = [];

    /** @psalm-var non-empty-string|null */
    private string|null $fieldTag = null;
    /** @psalm-var array|FieldAttributesClosure */
    private array|Closure $fieldAttributes = [];
    private string $fieldPrepend = '';
    private string $fieldAppend = '';
    private string $fieldTemplate = "{label}\n{value}";

    /** @psalm-var non-empty-string|null */
    private string|null $labelTag = 'dt';
    /** @psalm-var array|LabelAttributesClosure */
    private array|Closure $labelAttributes = [];
    private string $labelPrepend = '';
    private string $labelAppend = '';

    /** @psalm-var non-empty-string|null */
    private string|null $valueTag = 'dd';
    /** @psalm-var array|ValueAttributesClosure */
    private array|Closure $valueAttributes = [];
    private string $valuePrepend = '';
    private string $valueAppend = '';

    private ValuePresenterInterface $valuePresenter;

    public function __construct()
    {
        $this->valuePresenter = new SimpleValuePresenter();
    }

    /**
     * Return a new instance with the data.
     *
     * @param array|object $data The data model whose details are to be displayed. This can be an object or
     * an associative array.
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
     * @param DataField ...$fields The field configurations. Each object represents the configuration for one
     * particular field. For example,
     *
     * ```php
     * [
     *    new DataField('name', label: 'Name'),
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
     * Returns a new instance with the HTML tag name for the container.
     *
     * @param string|null $tag HTML tag name.
     */
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

    /**
     * Returns a new instance with the HTML tag name for the list.
     *
     * @param string|null $tag The HTML tag name.
     */
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

    /**
     * Returns a new instance with the HTML tag name for the field container.
     *
     * @param string|null $tag The HTML tag name.
     */
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

    /**
     * Add HTML content after the field container tag.
     *
     * @param string|Stringable ...$append The HTML content to be appended.
     * @return $this
     */
    public function fieldAppend(string|Stringable ...$append): self
    {
        $new = clone $this;
        $new->fieldAppend = implode('', $append);
        return $new;
    }

    /**
     * Return a new instance with the field template set.
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

    /**
     * Returns a new instance with the HTML tag name for the field label wrapper.
     * @param string|null $tag The HTML tag name or `null` to disable the wrapper.
     */
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
     * @psalm-param array|LabelAttributesClosure $attributes
     */
    public function labelAttributes(array|Closure $attributes): self
    {
        $new = clone $this;
        $new->labelAttributes = $attributes;
        return $new;
    }

    /**
     * Returns a new instance with the HTML content to be prepended to the label.
     *
     * @param string|Stringable ...$prepend The HTML content to be prepended.
     */
    public function labelPrepend(string|Stringable ...$prepend): self
    {
        $new = clone $this;
        $new->labelPrepend = implode('', $prepend);
        return $new;
    }

    /**
     * Returns a new instance with the HTML content to be appended to the label.
     * @param string|Stringable ...$append The HTML content to be appended.
     */
    public function labelAppend(string|Stringable ...$append): self
    {
        $new = clone $this;
        $new->labelAppend = implode('', $append);
        return $new;
    }

    /**
     * Return a new instance with the value tag.
     *
     * @param string|null $tag HTML tag.
     */
    public function valueTag(string|null $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->valueTag = $tag;
        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for the value tag.
     *
     * @param array|Closure $attributes Attribute values indexed by attribute names.
     *
     * @psalm-param array|ValueAttributesClosure $attributes
     */
    public function valueAttributes(array|Closure $attributes): self
    {
        $new = clone $this;
        $new->valueAttributes = $attributes;
        return $new;
    }

    /**
     * Returns a new instance with the HTML content to be prepended to the value.
     * @param string|Stringable ...$prepend The HTML content to be prepended.
     */
    public function valuePrepend(string|Stringable ...$prepend): self
    {
        $new = clone $this;
        $new->valuePrepend = implode('', $prepend);
        return $new;
    }

    /**
     * Returns a new instance with the HTML content to be appended to the value.
     * @param string|Stringable ...$append The HTML content to be appended.
     */
    public function valueAppend(string|Stringable ...$append): self
    {
        $new = clone $this;
        $new->valueAppend = implode('', $append);
        return $new;
    }

    /**
     * Returns a new instance with the value presenter set.
     * @param ValuePresenterInterface $presenter The value presenter.
     */
    public function valuePresenter(ValuePresenterInterface $presenter): self
    {
        $new = clone $this;
        $new->valuePresenter = $presenter;
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
                array_filter($this->fields, static fn(DataField $field): bool => $field->visible),
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

        $attributes = array_merge(
            $this->fieldAttributes instanceof Closure
                ? ($this->fieldAttributes)($context)
                : $this->fieldAttributes,
            $field->fieldAttributes instanceof Closure
                ? ($field->fieldAttributes)($context)
                : $field->fieldAttributes,
        );

        return Html::tag($this->fieldTag, "\n" . $content . "\n", $attributes)
            ->encode(false)
            ->render();
    }

    private function renderLabel(DataField $field): string
    {
        $content = $field->label ?? $field->property ?? '';
        if ($field->labelEncode) {
            $content = Html::encode($content);
        }

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
        $attributes = array_merge(
            $this->labelAttributes instanceof Closure
                ? ($this->labelAttributes)($context)
                : $this->labelAttributes,
            $field->labelAttributes instanceof Closure
                ? ($field->labelAttributes)($context)
                : $field->labelAttributes,
        );

        return Html::tag($this->labelTag, $content, $attributes)
            ->encode(false)
            ->render();
    }

    private function renderValue(DataField $field): string
    {
        $value = $this->getValue($field);
        $value = $this->valuePresenter->present($value);
        if ($field->valueEncode) {
            $value = Html::encode($value);
        }

        if ($this->valuePrepend !== '') {
            $value = $this->valuePrepend . "\n" . $value;
        }
        if ($this->valueAppend !== '') {
            $value .= "\n" . $this->valueAppend;
        }

        if ($this->valueTag === null) {
            return $value;
        }

        $context = new ValueContext($field, $this->data, $value);
        $attributes = array_merge(
            $this->valueAttributes instanceof Closure
                ? ($this->valueAttributes)($context)
                : $this->valueAttributes,
            $field->valueAttributes instanceof Closure
                ? ($field->valueAttributes)($context)
                : $field->valueAttributes,
        );

        return Html::tag($this->valueTag, $value, $attributes)
            ->encode(false)
            ->render();
    }

    private function getValue(DataField $field): mixed
    {
        if ($field->value === null) {
            /** @var string $property */
            $property = $field->property;
            return ArrayHelper::getValue($this->data, $property);
        }

        if ($field->value instanceof Closure) {
            return ($field->value)(
                new GetValueContext($field, $this->data)
            );
        }

        return $field->value;
    }
}
