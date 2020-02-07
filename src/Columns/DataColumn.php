<?php

namespace Yiisoft\Yii\DataView\Columns;

use Closure;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Html\Html;
use Yiisoft\Strings\Inflector;

/**
 * DataColumn is the default column type for the [[GridView]] widget.
 * It is used to show data columns and allows [[enableSorting|sorting]] and [[filter|filtering]] them.
 * A simple data column definition refers to an attribute in the data model of the
 * GridView's data provider. The name of the attribute is specified by [[attribute]].
 * By setting [[value]] and [[label]], the header and cell content can be customized.
 * A data column differentiates between the [[getDataCellValue|data cell value]] and the
 * [[renderDataCellContent|data cell content]]. The cell value is an un-formatted value that
 * may be used for calculation, while the actual cell content is a [[format|formatted]] version of that
 * value which may contain HTML markup.
 * For more details and usage information on DataColumn, see the [guide article on data
 * widgets](guide:output-data-widgets).
 */
class DataColumn extends Column
{
    /**
     * @var string the attribute name associated with this column. When neither [[content]] nor [[value]]
     *             is specified, the value of the specified attribute will be retrieved from each data model and
     *     displayed. Also, if [[label]] is not specified, the label associated with the attribute will be displayed.
     */
    private ?string $attribute;
    /**
     * @var string label to be displayed in the [[header|header cell]] and also to be used as the sorting
     *             link label when sorting is enabled for this column.
     *             If it is not set and the models provided by the GridViews data provider are instances
     *             of [[\Yiisoft\Db\ActiveRecord]], the label will be determined using
     *     [[\Yiisoft\Db\ActiveRecord::getAttributeLabel()]]. Otherwise [[\Yiisoft\Strings\Inflector::camel2words()]]
     *     will be used to get a label.
     */
    private ?string $label;
    /**
     * @var bool whether the header label should be HTML-encoded.
     * @see label
     */
    private bool $encodeLabel = true;
    /**
     * @var string|Closure an anonymous function or a string that is used to determine the value to display in the
     *     current column. If this is an anonymous function, it will be called for each row and the return value will
     *     be used as the value to display for every data model. The signature of this function should be: `function
     *     ($model, $key, $index, $column)`. Where `$model`, `$key`, and `$index` refer to the model, key and index of
     *     the row currently being rendered and `$column` is a reference to the [[DataColumn]] object. You may also set
     *     this property to a string representing the attribute name to be displayed in this column. This can be used
     *     when the attribute to be displayed is different from the [[attribute]] that is used for sorting and
     *     filtering. If this is not set, `$model[$attribute]` will be used to obtain the value, where `$attribute` is
     *     the value of [[attribute]].
     */
    private $value;
    /**
     * @var string|array|Closure in which format should the value of each data model be displayed as (e.g. `"raw"`,
     *     `"text"`, `"html"`,
     *                           `['date', 'php:Y-m-d']`). Supported formats are determined by the
     *     [[GridView::formatter|formatter]] used by the [[GridView]]. Default format is "text" which will format the
     *     value as an HTML-encoded plain text when
     *                           [[\yii\i18n\Formatter]] is used as the [[GridView::$formatter|formatter]] of the
     *     GridView.
     * @see \Yiisoft\I18n\MessageFormatterInterface::format()
     */
    private $format = 'text';
    /**
     * @var bool whether to allow sorting by this column. If true and [[attribute]] is found in
     *           the sort definition of [[GridView::dataProvider]], then the header cell of this column
     *           will contain a link that may trigger the sorting when being clicked.
     */
    private bool $enableSorting = true;
    /**
     * @var array the HTML attributes for the link tag in the header cell
     *            generated by [[\yii\data\Sort::link]] when sorting is enabled for this column.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    private array $sortLinkOptions = [];
    /**
     * @var string|array|null|false the HTML code representing a filter input (e.g. a text field, a dropdown list)
     *                              that is used for this data column. This property is effective only when
     *     [[GridView::filterModel]] is set.
     * - If this property is not set, a text field will be generated as the filter input with attributes defined
     *   with [[filterInputOptions]]. See [[\yii\helpers\BaseHtml::activeInput]] for details on how an active
     *   input tag is generated.
     * - If this property is an array, a dropdown list will be generated that uses this property value as
     *   the list options.
     * - If you don't want a filter for this data column, set this value to be false.
     */
    private $filter;
    /**
     * @var array the HTML attributes for the filter input fields. This property is used in combination with
     *            the [[filter]] property. When [[filter]] is not set or is an array, this property will be used to
     *            render the HTML attributes for the generated filter input fields.
     *            By default a `'class' => 'form-control'` element will be added if no class has been specified.
     *            If you do not want to create a class attribute, you can specify `['class' => null]`.
     * Empty `id` in the default value ensures that id would not be obtained from the model attribute thus
     * providing better performance.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    private array $filterInputOptions = [];

    public function attribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @param array|closure|string $format
     * @return $this
     */
    public function format($format): self
    {
        $this->format = $format;

        return $this;
    }

    public function label(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    protected function renderHeaderCellContent(): string
    {
        if ($this->header !== null || ($this->label === null && $this->attribute === null)) {
            return parent::renderHeaderCellContent();
        }

        $label = $this->getHeaderCellLabel();
        if ($this->encodeLabel) {
            $label = Html::encode($label);
        }

        if ($this->attribute !== null &&
            $this->enableSorting &&
            ($sort = $this->grid->getDataReader()->getSort()) !== null &&
            array_key_exists($this->attribute, $sort->getCriteria())
        ) {
            return $sort->link($this->attribute, array_merge($this->sortLinkOptions, ['label' => $label]));
        }

        return $label;
    }

    protected function getHeaderCellLabel(): string
    {
        $provider = $this->grid->getDataReader();

        if ($this->label === null) {
            if ($provider instanceof ActiveDataProvider && $provider->query instanceof ActiveQueryInterface) {
                /* @var $modelClass Model */
                $modelClass = $provider->query->modelClass;
                $model = $modelClass::instance();
                $label = $model->getAttributeLabel($this->attribute);
            } elseif ($provider instanceof ArrayDataProvider && $provider->modelClass !== null) {
                /* @var $modelClass Model */
                $modelClass = $provider->modelClass;
                $model = $modelClass::instance();
                $label = $model->getAttributeLabel($this->attribute);
            } elseif ($this->grid->getFilterModel() !== null && $this->grid->getFilterModel() instanceof Model) {
                $label = $this->grid->getFilterModel()->getAttributeLabel($this->attribute);
            } else {
                $models = $provider->read();
                if (($model = reset($models)) instanceof Model) {
                    /* @var $model Model */
                    $label = $model->getAttributeLabel($this->attribute);
                } else {
                    $inflector = new Inflector();
                    $label = $inflector->camel2words($this->attribute);
                }
            }
        } else {
            $label = $this->label;
        }

        return $label;
    }

    protected function renderFilterCellContent(): string
    {
        if (is_string($this->filter)) {
            return $this->filter;
        }

        $model = $this->grid->getFilterModel();

        if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive(
                $this->attribute
            )) {
            if ($model->hasErrors($this->attribute)) {
                Html::addCssClass($this->filterOptions, 'has-error');
                $error = ' ' . Html::error($model, $this->attribute, $this->grid->getFilterErrorOptions());
            } else {
                $error = '';
            }

            $filterOptions = array_merge(['class' => 'form-control', 'id' => null], $this->filterInputOptions);
            if (is_array($this->filter)) {
                $options = array_merge(['prompt' => ''], $filterOptions);

                return Html::activeDropDownList($model, $this->attribute, $this->filter, $options) . $error;
            }
            if ($this->format === 'boolean') {
                $options = array_merge(['prompt' => ''], $filterOptions);

                return Html::activeDropDownList(
                        $model,
                        $this->attribute,
                        [
                            1 => $this->grid->getMessageFormatter()->booleanFormat[1],
                            0 => $this->grid->getMessageFormatter()->booleanFormat[0],
                        ],
                        $options
                    ) . $error;
            }

            return Html::activeTextInput($model, $this->attribute, $filterOptions) . $error;
        }

        return parent::renderFilterCellContent();
    }

    /**
     * Returns the data cell value.
     *
     * @param mixed $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the models array returned by
     *     [[GridView::dataProvider]].
     * @return string the data cell value
     */
    public function getDataCellValue(array $model, $key, int $index): string
    {
        if ($this->value !== null) {
            if (is_string($this->value)) {
                return ArrayHelper::getValue($model, $this->value);
            }

            return call_user_func($this->value, $model, $key, $index, $this);
        }
        if ($this->attribute !== null) {
            return ArrayHelper::getValue($model, $this->attribute);
        }

        return null;
    }

    protected function renderDataCellContent(array $model, $key, int $index): string
    {
        if ($this->content === null) {
            return $this->formatMessage(
                $this->getDataCellValue($model, $key, $index),
                [$this->format]
            );
        }

        return parent::renderDataCellContent($model, $key, $index);
    }
}
