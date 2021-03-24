<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Closure;
use JsonException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Html\Html;
use Yiisoft\Strings\Inflector;
use Yiisoft\Yii\DataView\Exception\InvalidConfigException;

use function array_key_exists;
use function implode;
use function is_array;
use function is_object;
use function is_string;
use function preg_match;
use function strtr;

/**
 * DetailView displays the detail of a single data {@see model}.
 *
 * DetailView is best used for displaying a model in a regular format (e.g. each model attribute is displayed as a row
 * in a table.) The model can be either an instance of object or an associative array.
 *
 * DetailView uses the {@see attributes} property to determines which model attributes should be displayed and how they
 * should be formatted.
 *
 * A typical usage of DetailView is as follows:
 * ```php
 * ```
 * For more details and usage information on DetailView:
 * see the [guide article on data widgets](guide:output-data-widgets).
 */
final class DetailView extends Widget
{
    /** @var array|object|null */
    private $model = null;
    private array $attributes = [];
    private array $captionOptions = [];
    private array $contentOptions = [];
    private Inflector $inflector;
    private array $rowOptions = [];
    private string $template = '<tr{rowOptions}><th{captionOptions}>{label}</th><td{contentOptions}>{value}</td></tr>';
    private array $options = ['class' => 'table table-striped table-bordered detail-view'];

    public function __construct(Inflector $inflector, Html $html)
    {
        $this->inflector = $inflector;
    }

    /**
     * Renders the detail view.
     *
     * This is the main entry of the whole detail view rendering.
     *
     * @throws InvalidConfigException|JsonException
     *
     * @return string the result of widget execution to be outputted.
     */
    public function run(): string
    {
        if ($this->model === null) {
            throw new InvalidConfigException('Please specify the "model" property.');
        }

        $normalizedAttributes = $this->normalizeAttributes();

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId() . '-detailview';
        }

        $i = 1;
        $rows = [];

        /** @var array<array-key,mixed> */
        foreach ($normalizedAttributes as $attribute) {
            $rows[] = $this->renderAttribute($attribute, $i++);
        }

        $options = $this->options;

        /** @psalm-var non-empty-string */
        $tag = $options['tag'] ?? 'table';

        unset($options['tag']);

        return Html::tag($tag, "\n" . implode("\n", $rows) . "\n", $options)->encode(false)->render();
    }

    /**
     * @param array $attributes a list of attributes to be displayed in the detail view. Each array element represents
     * the specification for displaying one particular attribute.
     *
     * An attribute can be specified as a string in the format of `attribute`, `attribute:format` or
     * attribute:format:label`, where `attribute` refers to the attribute name, and `format` represents the format of
     * the attribute. The `format` is passed to the {@see Formatter::format()} method to format an attribute value
     * into a displayable text. Please refer to {@see Formatter} for the supported types. Both `format` and `label` are
     * optional. They will take default values if absent. An attribute can also be specified in terms of an array with
     * the following elements:
     * - `attribute`: the attribute name. This is required if either `label` or `value` is not specified.
     * - `label`: the label associated with the attribute. If this is not specified, it will be generated from the
     * attribute name.
     * - `value`: the value to be displayed. If this is not specified, it will be retrieved from {@see model} using the
     * attribute name by calling {@see ArrayHelper::getValue()}. Note that this value will be formatted into a
     * displayable text according to the `format` option. It can be defined as closure with the following parameters:
     *
     *   ```php
     *   function ($model, $widget)
     *   ```
     *   `$model` refers to displayed model and `$widget` is an instance of `DetailView` widget.
     * - `format`: the type of the value that determines how the value would be formatted into a displayable text.
     * Please refer to {@see Formatter} for supported types and {@see Formatter::format()} on how to specify this value.
     * - `visible`: whether the attribute is visible. If set to `false`, the attribute will NOT be displayed.
     * - `contentOptions`: the HTML attributes to customize value tag. For example: `['class' => 'bg-red']`.
     * Please refer to {@see Html::renderTagAttributes()} for the supported syntax.
     * - `captionOptions`: the HTML attributes to customize label tag. For example: `['class' => 'bg-red']`.
     * Please refer to {@see Html::renderTagAttributes()} for the supported syntax.
     *
     * @return DetailView
     */
    public function attributes(array $attributes): self
    {
        $new = clone $this;
        $new->attributes = $attributes;

        return $new;
    }

    /**
     * @param array $captionOptions the `HTML` attributes for customize all labels tag.
     *
     * The `tag` option specifies what container tag should be used. It defaults to `table` if not set.
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     *
     * @return $this
     */
    public function captionOptions(array $captionOptions): self
    {
        $new = clone $this;
        $new->captionOptions = $captionOptions;

        return $new;
    }

    /**
     * @param array $contentOptions the `HTML` attributes for customize all values tag.
     *
     * The `tag` option specifies what container tag should be used. It defaults to `table` if not set.
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     *
     * @return $this
     */
    public function contentOptions(array $contentOptions): self
    {
        $new = clone $this;
        $new->contentOptions = $contentOptions;

        return $new;
    }

    /**
     * @param array|object $model the data model whose details are to be displayed.
     *
     * This can be a {@see object} instance, an associative {@see array}, an object that implements
     * {@see \Yiisoft\Arrays\ArrayableInterface} interface or simply an object with defined public accessible non-static
     * properties.
     *
     * @psalm-suppress DocblockTypeContradiction
     *
     * @return self
     */
    public function model($model): self
    {
        if (!is_array($model) && !is_object($model)) {
            throw new InvalidConfigException('The "model" property must be either an array or an object.');
        }

        $new = clone $this;
        $new->model = $model;

        return $new;
    }

    /**
     * @param array $options the HTML attributes for the container tag of this widget. The `tag` option specifies what
     * container tag should be used. It defaults to `table` if not set.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function options(array $options): self
    {
        $new = clone $this;
        $new->options = $options;

        return $new;
    }

    /**
     * @param array $rowOptions the `HTML` attributes for customize all rows data.
     *
     * The `tag` option specifies what container tag should be used. It defaults to `table` if not set.
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     *
     * @return $this
     */
    public function rowOptions(array $rowOptions): self
    {
        $new = clone $this;
        $new->rowOptions = $rowOptions;

        return $new;
    }

    /**
     * @param string $template the template used to render a single attribute.
     *
     * The tokens `{captionOptions}`, `{contentOptions}` and `{rowOptions}` are available, which will represent HTML
     * attributes of HTML container elements for the label and value.
     *
     * @return $this
     */
    public function template(string $template): self
    {
        $new = clone $this;
        $new->template = $template;

        return $new;
    }

    /**
     * Renders a single attribute.
     *
     * @param array $attribute the specification of the attribute to be rendered.
     * @param int $index the zero-based index of the attribute in the {@see attributes} array.
     *
     * @throws JsonException
     *
     * @return string the rendering result
     */
    private function renderAttribute(array $attribute, int $index): string
    {
        /** @var array */
        $captionOptions = $attribute['captionOptions'] ?? [];
        $captionOptions = array_merge_recursive($this->captionOptions, $captionOptions);

        /** @var array */
        $contentOptions = $attribute['contentOptions'] ?? [];
        $contentOptions = array_merge_recursive($this->contentOptions, $contentOptions);

        /** @var array */
        $rowOptions = $attribute['rowOptions'] ?? [];
        $rowOptions = array_merge_recursive($this->rowOptions, $rowOptions);

        return strtr($this->template, [
            '{label}' => $attribute['label'],
            '{value}' => $attribute['value'],
            '{captionOptions}' => Html::renderTagAttributes($captionOptions),
            '{contentOptions}' => Html::renderTagAttributes($contentOptions),
            '{rowOptions}' => Html::renderTagAttributes($rowOptions),
        ]);
    }

    /**
     * Normalizes the attribute specifications.
     *
     * @throws InvalidConfigException
     */
    private function normalizeAttributes(): array
    {
        $attributes = $this->attributes;

        /** @var array<array-key,array|string|Closure> $attributes */
        foreach ($attributes as $i => $attribute) {
            if (is_string($attribute)) {
                if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $attribute, $matches)) {
                    throw new InvalidConfigException(
                        'The attribute must be specified in the format of "attribute", "attribute:format" or '
                        . '"attribute:format:label" '
                    );
                }

                $attribute = [
                    'attribute' => $matches[1],
                    'format' => $matches[3] ?? 'text',
                    'label' => $matches[5] ?? null,
                ];
            }

            if (is_array($attribute)) {
                if (isset($attribute['visible']) && !$attribute['visible']) {
                    unset($attributes[$i]);
                    continue;
                }

                if (!isset($attribute['format'])) {
                    $attribute['format'] = 'text';
                }

                if (isset($attribute['attribute'])) {
                    /** @var string */
                    $attributeName = $attribute['attribute'];
                    if (!isset($attribute['label'])) {
                        $attribute['label'] = $this->inflector->toHumanReadable($attributeName, true);
                    }

                    if (!array_key_exists('value', $attribute) && $this->model !== null) {
                        /** @var mixed */
                        $attribute['value'] = ArrayHelper::getValue($this->model, $attributeName);
                    }
                } elseif (!isset($attribute['label']) || !array_key_exists('value', $attribute)) {
                    throw new InvalidConfigException('The attribute configuration requires the "attribute" element to determine the value and display label.');
                }

                if ($attribute['value'] instanceof Closure) {
                    /** @var mixed */
                    $attribute['value'] = $attribute['value']($this->model, $this);
                }
            }

            $attributes[$i] = $attribute;
        }

        return $attributes;
    }
}
