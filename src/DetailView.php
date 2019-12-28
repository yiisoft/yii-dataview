<?php

namespace Yiisoft\Yii\DataView;

use Closure;
use Yiisoft\Arrays\ArrayableInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Factory\Exceptions\InvalidConfigException;
use Yiisoft\Html\Html;
use Yiisoft\I18n\MessageFormatterInterface;
use Yiisoft\Strings\Inflector;

/**
 * DetailView displays the detail of a single data [[model]].
 * DetailView is best used for displaying a model in a regular format (e.g. each model attribute
 * is displayed as a row in a table.) The model can be either an instance of [[Model]]
 * or an associative array.
 * DetailView uses the [[attributes]] property to determines which model attributes
 * should be displayed and how they should be formatted.
 * A typical usage of DetailView is as follows:
 * ```php
 * echo DetailView::widget([
 *     'model' => $model,
 *     'attributes' => [
 *         'title',               // title attribute (in plain text)
 *         'description:html',    // description attribute in HTML
 *         [                      // the owner name of the model
 *             'label' => 'Owner',
 *             'value' => $model->owner->name,
 *         ],
 *         'created_at:datetime', // creation date formatted as datetime
 *     ],
 * ]);
 * ```
 * For more details and usage information on DetailView, see the [guide article on data
 * widgets](guide:output-data-widgets).
 */
class DetailView
{
    /**
     * @var array|object the data model whose details are to be displayed. This can be a [[Model]] instance,
     *                   an associative array, an object that implements [[Arrayable]] interface or simply an object
     *     with defined public accessible non-static properties.
     */
    public $model;
    /**
     * @var array a list of attributes to be displayed in the detail view. Each array element
     *            represents the specification for displaying one particular attribute.
     * An attribute can be specified as a string in the format of `attribute`, `attribute:format` or
     *     `attribute:format:label`, where `attribute` refers to the attribute name, and `format` represents the format
     *     of the attribute. The `format` is passed to the [[Formatter::format()]] method to format an attribute value
     *     into a displayable text. Please refer to [[Formatter]] for the supported types. Both `format` and `label`
     *     are optional. They will take default values if absent. An attribute can also be specified in terms of an
     *     array with the following elements:
     * - `attribute`: the attribute name. This is required if either `label` or `value` is not specified.
     * - `label`: the label associated with the attribute. If this is not specified, it will be generated from the
     *     attribute name.
     * - `value`: the value to be displayed. If this is not specified, it will be retrieved from [[model]] using the
     *     attribute name by calling [[ArrayHelper::getValue()]]. Note that this value will be formatted into a
     *     displayable text according to the `format` option. Since version 2.0.11 it can be defined as closure with
     *     the following parameters:
     *   ```php
     *   function ($model, $widget)
     *   ```
     *   `$model` refers to displayed model and `$widget` is an instance of `DetailView` widget.
     * - `format`: the type of the value that determines how the value would be formatted into a displayable text.
     *   Please refer to [[Formatter]] for supported types and [[Formatter::format()]] on how to specify this value.
     * - `visible`: whether the attribute is visible. If set to `false`, the attribute will NOT be displayed.
     * - `contentOptions`: the HTML attributes to customize value tag. For example: `['class' => 'bg-red']`.
     *   Please refer to [[\yii\helpers\BaseHtml::renderTagAttributes()]] for the supported syntax.
     * - `captionOptions`: the HTML attributes to customize label tag. For example: `['class' => 'bg-red']`.
     *   Please refer to [[\yii\helpers\BaseHtml::renderTagAttributes()]] for the supported syntax.
     */
    protected array $attributes = [];
    /**
     * @var string|callable the template used to render a single attribute. If a string, the token `{label}`
     *                      and `{value}` will be replaced with the label and the value of the corresponding attribute.
     *                      If a callback (e.g. an anonymous function), the signature must be as follows:
     * ```php
     * function ($attribute, $index, $widget)
     * ```
     * where `$attribute` refer to the specification of the attribute being rendered, `$index` is the zero-based
     * index of the attribute in the [[attributes]] array, and `$widget` refers to this widget instance.
     * Since Version 2.0.10, the tokens `{captionOptions}` and `{contentOptions}` are available, which will represent
     * HTML attributes of HTML container elements for the label and value.
     */
    protected $template = '<tr><th{captionOptions}>{label}</th><td{contentOptions}>{value}</td></tr>';
    /**
     * @var array the HTML attributes for the container tag of this widget. The `tag` option specifies
     *            what container tag should be used. It defaults to `table` if not set.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    protected array $options = ['class' => 'table table-striped table-bordered detail-view'];
    /**
     * @var \Yiisoft\I18n\MessageFormatterInterface the formatter used to format model attribute values into
     *     displayable texts.
     */
    protected static MessageFormatterInterface $messageFormatter;

    public function __construct(MessageFormatterInterface $formatter)
    {
        self::$messageFormatter = $formatter;
    }

    public static function widget(): self
    {
        return new static(self::$messageFormatter);
    }

    /**
     * Initializes the detail view.
     * This method will initialize required property values.
     *
     * @throws \Yiisoft\Factory\Exceptions\InvalidConfigException
     */
    public function init(): self
    {
        if ($this->model === null) {
            throw new InvalidConfigException('Please specify the "model" property.');
        }
        $this->normalizeAttributes();

        return $this;
    }

    /**
     * Renders the detail view.
     * This is the main entry of the whole detail view rendering.
     *
     * @return string the result of widget execution to be outputted.
     */
    public function run(): string
    {
        $rows = [];
        $i = 0;
        foreach ($this->attributes as $attribute) {
            $rows[] = $this->renderAttribute($attribute, $i++);
        }

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'table');

        return Html::tag($tag, implode("\n", $rows), $options);
    }

    /**
     * Renders a single attribute.
     *
     * @param array $attribute the specification of the attribute to be rendered.
     * @param int $index the zero-based index of the attribute in the [[attributes]] array
     * @return string the rendering result
     */
    protected function renderAttribute($attribute, $index): string
    {
        if (is_string($this->template)) {
            $captionOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'captionOptions', []));
            $contentOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'contentOptions', []));

            // TODO fix
            $language = 'language';

            return strtr(
                $this->template,
                [
                    '{label}' => $attribute['label'],
                    '{value}' => self::$messageFormatter->format(
                        $attribute['value'],
                        [$attribute['format']],
                        $language
                    ),
                    '{captionOptions}' => $captionOptions,
                    '{contentOptions}' => $contentOptions,
                ]
            );
        }

        return call_user_func($this->template, $attribute, $index, $this);
    }

    /**
     * Normalizes the attribute specifications.
     *
     * @throws InvalidConfigException
     */
    protected function normalizeAttributes(): void
    {
        if ($this->attributes === []) {
            if ($this->model instanceof Model) {
                $this->attributes = $this->model->attributes();
            } elseif (is_object($this->model)) {
                $this->attributes = $this->model instanceof ArrayableInterface ? array_keys($this->model->toArray())
                    : array_keys(get_object_vars($this->model));
            } elseif (is_array($this->model)) {
                $this->attributes = array_keys($this->model);
            } else {
                throw new InvalidConfigException('The "model" property must be either an array or an object.');
            }
            sort($this->attributes);
        }

        foreach ($this->attributes as $i => $attribute) {
            if (is_string($attribute)) {
                if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $attribute, $matches)) {
                    throw new InvalidConfigException(
                        'The attribute must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"'
                    );
                }

                $attribute = [
                    'attribute' => $matches[1],
                    'format' => $matches[3] ?? 'text',
                    'label' => $matches[5] ?? null,
                ];
            }

            if (!is_array($attribute)) {
                throw new InvalidConfigException('The attribute configuration must be an array.');
            }

            if (isset($attribute['visible']) && !$attribute['visible']) {
                unset($this->attributes[$i]);
                continue;
            }

            if (!isset($attribute['format'])) {
                $attribute['format'] = 'text';
            }
            if (isset($attribute['attribute'])) {
                $attributeName = $attribute['attribute'];
                if (!isset($attribute['label'])) {
                    $inflector = new Inflector();

                    $attribute['label'] = $this->model instanceof Model
                        ? $this->model->getAttributeLabel($attributeName)
                        : $inflector->camel2words($attributeName, true);
                }
                if (!array_key_exists('value', $attribute)) {
                    $attribute['value'] = ArrayHelper::getValue($this->model, $attributeName);
                }
            } elseif (!isset($attribute['label']) || !array_key_exists('value', $attribute)) {
                throw new InvalidConfigException(
                    'The attribute configuration requires the "attribute" element to determine the value and display label.'
                );
            }

            if ($attribute['value'] instanceof Closure) {
                $attribute['value'] = call_user_func($attribute['value'], $this->model, $this);
            }

            $this->attributes[$i] = $attribute;
        }
    }

    /**
     * @param array|object $model
     * @return static
     */
    public function withModel($model): self
    {
        $this->model = $model;

        return $this;
    }

    public function withTemplate(string $string): self
    {
        $this->template = $string;

        return $this;
    }

    public function withAttributes(array $array): self
    {
        $this->attributes = $array;
        $this->normalizeAttributes();

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
