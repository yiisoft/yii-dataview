<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Columns;

use Closure;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Factory\Exceptions\InvalidConfigException;
use Yiisoft\Html\Html;
use Yiisoft\Json\Json;

/**
 * CheckboxColumn displays a column of checkboxes in a grid view.
 * To add a CheckboxColumn to the [[GridView]], add it to the [[GridView::columns|columns]] configuration as follows:
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         '__class' => \yii\grid\CheckboxColumn::class,
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 * Users may click on the checkboxes to select rows of the grid. The selected rows may be
 * obtained by calling the following JavaScript code:
 * ```javascript
 * var keys = $('#grid').yiiGridView('getSelectedRows');
 * // keys is an array consisting of the keys associated with the selected rows
 * ```
 * For more details and usage information on CheckboxColumn, see the [guide article on data
 * widgets](guide:output-data-widgets).
 */
class CheckboxColumn extends Column
{
    /**
     * @var string the name of the input checkbox input fields. This will be appended with `[]` to ensure it is an
     *     array.
     */
    protected string $name = 'selection';
    /**
     * @var array|callable the HTML attributes for checkboxes. This can either be an array of
     *                     attributes or an anonymous function ([[Closure]]) that returns such an array.
     *                     The signature of the function should be the following: `function ($model, $key, $index,
     *     $column)`. Where `$model`, `$key`, and `$index` refer to the model, key and index of the row currently being
     *     rendered and `$column` is a reference to the [[CheckboxColumn]] object. A function may be used to assign
     *     different attributes to different rows based on the data in that row. Specifically if you want to set a
     *     different value for the checkbox you can use this option in the following way (in this example using the
     *     `name` attribute of the model):
     * ```php
     * 'checkboxOptions' => function ($model, $key, $index, $column) {
     *     return ['value' => $model->name];
     * }
     * ```
     *
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    protected $checkboxOptions = [];
    /**
     * @var bool whether it is possible to select multiple rows. Defaults to `true`.
     */
    protected bool $multiple = true;
    /**
     * @var string the css class that will be used to find the checkboxes.
     */
    protected ?string $cssClass = null;

    /**
     * @throws InvalidConfigException if [[name]] is not set.
     */
    protected function init(): void
    {
        if ('' === $this->name) {
            throw new InvalidConfigException('The "name" property must be set.');
        }
        if (substr_compare($this->name, '[]', -2, 2)) {
            $this->name .= '[]';
        }

        $this->registerClientScript();
    }

    public function name(string $string): self
    {
        $this->name = $string;

        return $this;
    }

    /**
     * @param array|callable $array
     *
     * @return $this
     */
    public function checkboxOptions($array): self
    {
        $this->checkboxOptions = ArrayHelper::merge($this->checkboxOptions, $array);

        return $this;
    }

    /**
     * @param Closure|string $param
     *
     * @return $this
     */
    public function content($param): self
    {
        $this->content = $param;

        return $this;
    }

    /**
     * Renders the header cell content.
     * The default implementation simply renders [[header]].
     * This method may be overridden to customize the rendering of the header cell.
     *
     * @return string the rendering result
     */
    protected function renderHeaderCellContent(): string
    {
        $this->init();

        if ($this->header !== null || !$this->multiple) {
            return parent::renderHeaderCellContent();
        }

        return Html::checkbox($this->getHeaderCheckboxName($this->name), false, ['class' => 'select-on-check-all']);
    }

    protected function renderDataCellContent(array $model, $key, int $index): string
    {
        $this->init();

        if ($this->content !== null) {
            return parent::renderDataCellContent($model, $key, $index);
        }

        if (\is_callable($this->checkboxOptions)) {
            $options = \call_user_func($this->checkboxOptions, $model, $key, $index, $this);
        } else {
            $options = $this->checkboxOptions;
        }

        if (!isset($options['value'])) {
            $options['value'] = \is_array($key) ? Json::encode($key) : $key;
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
        $id = $this->grid->getId();
        $options = Json::encode(
            [
                'name' => $this->name,
                'class' => $this->cssClass,
                'multiple' => $this->multiple,
                'checkAll' => $this->grid->isShowHeader() ? $this->getHeaderCheckboxName($this->name) : null,
            ]
        );
        $this->grid->getView()->registerJs("jQuery('#$id').yiiGridView('setSelectionColumn', $options);");
    }
}
