<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Columns;

use Closure;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Html\Html;
use Yiisoft\Json\Json;
use Yiisoft\Yii\DataView\Exception\InvalidConfigException;

use function call_user_func;
use function is_array;

/**
 * RadioButtonColumn displays a column of radio buttons in a grid view.
 *
 * To add a RadioButtonColumn to the {@see GridView}, add it to the {@see GridView::columns|columns} configuration as
 * follows:
 *
 * ```php
 * [
 *     'class' => RadioButtonColumn::class,
 *     'header()' => ['#'],
 * ];
 * ```
 */
final class RadioButtonColumn extends Column
{
    /** @var array|Closure */
    private $radioOptions = [];
    private string $name = 'radioButtonSelection';

    /**
     * @param string $name the name of the input radio button input fields.
     */
    public function name(string $name): self
    {
        if (empty($name)) {
            throw new InvalidConfigException('The "name" property it cannot be empty.');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @param array|Closure $radioOptions the HTML attributes for the radio buttons. This can either be an array of
     * attributes or an anonymous function ({@see Closure}) returning such an array.
     *
     * The signature of the function should be as follows: `function ($model, $key, $index, $column)` where
     * `$model`, `$key`, and `$index` refer to the model, key and index of the row currently being rendered and
     * `$column` is a reference to the {@see RadioButtonColumn} object.
     *
     * A function may be used to assign different attributes to different rows based on the data in that row.
     *
     * Specifically if you want to set a different value for the radio button you can use this option in the following
     * way (in this example using the `name` attribute of the model):
     *
     * ```php
     * 'radioOptions' => function ($model, $key, $index, $column) {
     *     return ['value' => $model['attribute'];
     * }
     * ```
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function radioOptions(array|Closure $radioOptions): self
    {
        if ($radioOptions instanceof Closure) {
            $this->radioOptions = $radioOptions;
        } elseif (is_array($this->radioOptions)) {
            $this->radioOptions = ArrayHelper::merge($this->radioOptions, $radioOptions);
        }

        return $this;
    }

    /**
     * Renders the data cell content.
     *
     * @param array|object $model the data model.
     * @param mixed $key the key associated with the data model.
     * @param int $index the zero-based index of the data model among the models array returned by
     * {@see GridView::dataReader}.
     *
     * @return string the rendering result.
     */
    protected function renderDataCellContent($model, $key, int $index): string
    {
        if (!empty($this->content)) {
            return parent::renderDataCellContent($model, $key, $index);
        }

        if ($this->radioOptions instanceof Closure) {
            /** @var array */
            $options = call_user_func($this->radioOptions, $model, $key, $index, $this);
        } else {
            $options = $this->radioOptions;

            if (!isset($options['value'])) {
                /** @var mixed */
                $options['value'] = is_array($key) ? Json::encode($key) : $key;
            }
        }

        /** @var bool */
        $checked = $options['checked'] ?? false;

        return Html::radio($this->name, $checked)
            ->addAttributes($options)
            ->render();
    }
}
