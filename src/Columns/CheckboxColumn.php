<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Columns;

use Closure;
use JsonException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Yii\DataView\Exception\InvalidConfigException;
use Yiisoft\Html\Html;
use Yiisoft\Json\Json;

use function call_user_func;
use function is_array;
use function is_callable;
use function substr;
use function substr_compare;

/**
 * CheckboxColumn displays a column of checkboxes in a grid view.
 * To add a CheckboxColumn to the {@see GridView}, add it to the {@see GridView::columns|columns} configuration as
 * follows:
 *
 * ```php
 * [
 *     '__class' => CheckBoxColumn::class,
 *     'header()' => ['#'],
 * ];
 * ```
 *
 * Users may click on the checkboxes to select rows of the grid. The selected rows may be obtained by calling the
 * following JavaScript code:
 *
 * For more details and usage information on CheckboxColumn:
 * see the [guide article on data widgets](guide:output-data-widgets).
 */
final class CheckboxColumn extends Column
{
    /** @var array|callable */
    protected $checkboxOptions = [];
    protected string $name = 'selection';
    protected bool $multiple = true;
    protected ?string $cssClass = null;

    public function __construct()
    {
        if ($this->name === '') {
            throw new InvalidConfigException('The "name" property must be set.');
        }

        if (substr_compare($this->name, '[]', -2, 2)) {
            $this->name .= '[]';
        }

        $this->registerClientScript();
    }

    /**
     * @param string|null $cssClass the css class that will be used to find the checkboxes.
     *
     * @return $this
     */
    public function cssClass(?string $cssClass): self
    {
        $this->cssClass = $cssClass;

        return $this;
    }

    /**
     * @param string $name the name of the input checkbox input fields. This will be appended with `[]` to ensure it is
     * an array.
     *
     * @return $this
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param array|callable $checkBoxOptions the HTML attributes for checkboxes. This can either be an array of
     * attributes or an anonymous function ({@see Closure}) that returns such an array. The signature of the function
     * should be the following:
     *
     * `function ($model, $key, $index, $column)`. Where `$model`, `$key`, and `$index` refer to the model, key and
     * index of the row currently being rendered and `$column` is a reference to the {@see CheckboxColumn} object.
     *
     * A function may be used to assign different attributes to different rows based on the data in that row.
     * Specifically if you want to set a different value for the checkbox you can use this option in the following way
     * (in this example using the `name` attribute of the model):
     *
     * ```php
     * 'checkboxOptions' => static function ($model, $key, $index, $column): array {
     *     return ['value' => $model['name'];
     * }
     * ```
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function checkboxOptions($checkBoxOptions): self
    {
        $this->checkboxOptions = ArrayHelper::merge($this->checkboxOptions, $checkBoxOptions);

        return $this;
    }

    /**
     * @param bool $multiple whether it is possible to select multiple rows. Defaults to `true`.
     */
    public function multiple(bool $multiple): void
    {
        $this->multiple = $multiple;
    }

    /**
     * Renders the header cell content.
     *
     * The default implementation simply renders {@see header}.
     * This method may be overridden to customize the rendering of the header cell.
     *
     * @throws JsonException
     *
     * @return string the rendering result
     */
    protected function renderHeaderCellContent(): string
    {
        if ($this->header !== null || !$this->multiple) {
            return parent::renderHeaderCellContent();
        }

        return Html::checkbox($this->getHeaderCheckboxName($this->name), false, ['class' => 'select-on-check-all']);
    }

    protected function renderDataCellContent(array $model, $key, int $index): string
    {
        if ($this->content !== null) {
            return parent::renderDataCellContent($model, $key, $index);
        }

        if (is_callable($this->checkboxOptions)) {
            $options = call_user_func($this->checkboxOptions, $model, $key, $index, $this);
        } else {
            $options = $this->checkboxOptions;
        }

        if (!isset($options['value'])) {
            $options['value'] = is_array($key) ? Json::encode($key) : $key;
        }

        if ($this->cssClass !== null) {
            Html::addCssClass($options, $this->cssClass);
        }

        return Html::checkbox($this->name, !empty($options['checked']), $options);
    }

    /**
     * Returns header checkbox name.
     *
     * @param string $name
     *
     * @return string header checkbox name
     */
    protected function getHeaderCheckboxName(string $name): string
    {
        if (substr_compare($name, '[]', -2, 2) === 0) {
            $name = substr($name, 0, -2);
        }
        if (substr_compare($name, ']', -1, 1) === 0) {
            $name = substr($name, 0, -1) . '_all]';
        } else {
            $name .= '_all';
        }

        return $name;
    }

    /**
     * Registers the needed JavaScript.
     */
    protected function registerClientScript(): void
    {
    }
}
