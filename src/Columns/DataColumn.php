<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Columns;

use Closure;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Html\Html;
use Yiisoft\Strings\Inflector;
use Yiisoft\Yii\DataView\Widget\LinkSorter;

use function array_merge;
use function call_user_func;
use function is_string;

/**
 * DataColumn is the default column type for the {@see GridView} widget.
 *
 * It is used to show data columns and allows {@see sorting} and {@see filter} them.
 *
 * A simple data column definition refers to an attribute in the data model of the GridView's data provider. The name of
 * the attribute is specified by {@see attribute}. By setting {@see value} and {@see label}, the header and cell content
 * can be customized.
 *
 * A data column differentiates between the {@see getDataCellValue|data cell value} and the
 * {@see renderDataCellContent|data cell content}. The cell value is an un-formatted value that may be used for
 * calculation, while the actual cell content is a {@see format|formatted} version of that value which may contain HTML
 * markup.
 *
 * ```php
 * [
 *     'attribute()' => ['registration_ip'],
 *     'label()' => ['Ip'],
 *     'value()' => static function ($model) {
 *         return $model['registration_ip'] === null
 *             ? '(not set)'
 *             : $model['registration_ip'];
 *     },
 * ];
 * ```
 *
 * For more details and usage information on DataColumn:
 * see the [guide article on data widgets](guide:output-data-widgets).
 */
final class DataColumn extends Column
{
    /** @var array|Closure|string */
    private $format = 'text';
    private string $filter = '';
    /** @var Closure|string|null */
    private $value;
    private string $attribute = '';
    private string $label = '';
    private bool $encodeLabel = true;
    private bool $enableSorting = true;
    private array $sortLinkOptions = [];
    private array $filterInputOptions = [];
    public string $filterAttribute = '';
    /** @var bool|float|int|string|null */
    private $filterValueDefault = null;

    /**
     * @param string $attribute the attribute name associated with this column. When neither {@see content} nor
     * {@see value} is specified, the value of the specified attribute will be retrieved from each data model and
     * displayed. Also, if {@see label} is not specified, the label associated with the attribute will be displayed.
     *
     * @return $this
     */
    public function attribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @param string the HTML code representing a filter input (e.g. a text field, a dropdown list) that is used for
     * this data column. This property is effective only when {@see filterModelName} is set.
     *
     * @return $this
     */
    public function filter(string $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @param array $filterInputOptions the HTML attributes for the filter input fields. This property is used in
     * combination with the {@see filter} property. When {@see filter} is not set or is an array, this property will be
     * used to render the HTML attributes for the generated filter input fields.
     *
     * By default a `'class' => 'form-control'` element will be added if no class has been specified.
     *
     * If you do not want to create a class attribute, you can specify `['class' => null]`.
     *
     * Empty `id` in the default value ensures that id would not be obtained from the model attribute thus providing
     * better performance.
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function filterInputOptions(array $filterInputOptions): self
    {
        $this->filterInputOptions = $filterInputOptions;

        return $this;
    }

    /**
     * Set filter value default text input field.
     *
     * @param bool|float|int|string|null $filterValueDefault
     *
     * @return $this
     */
    public function filterValueDefault($filterValueDefault): self
    {
        $this->filterValueDefault = $filterValueDefault;

        return $this;
    }

    /**
     * @param string $label to be displayed in the {@see header|header cell} and also to be used as the sorting link
     * label when sorting is enabled for this column. If it is not set and the models provided by the GridViews data
     * provider are instances.
     *
     * Otherwise {@see Inflector::toHumanReadable()} will be used to get a label.
     *
     * @return $this
     */
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Returns the data cell value.
     *
     * @param array|object $model the data model.
     * @param mixed $key the key associated with the data model.
     * @param int $index the zero-based index of the data model among the models array returned by
     * {@see GridView::dataReader}.
     *
     * @return string the data cell value.
     */
    public function getDataCellValue($model, $key, int $index): string
    {
        if ($this->value !== null) {
            if (is_string($this->value)) {
                return (string) ArrayHelper::getValue($model, $this->value);
            }

            return (string) call_user_func($this->value, $model, $key, $index, $this);
        }

        if ($this->attribute !== '') {
            return (string) ArrayHelper::getValue($model, $this->attribute);
        }

        return '';
    }

    /**
     * @param array $sortLinkOptions the HTML attributes for the link tag in the header cell generated by
     * {@see Sort::link} when sorting is enabled for this column.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function sortLinkOptions(array $sortLinkOptions): self
    {
        $this->sortLinkOptions = $sortLinkOptions;

        return $this;
    }

    /**
     * When HTML tag header labels should not be encoded.
     *
     * @return $this
     *
     * @see label
     */
    public function notEncodeLabel(): self
    {
        $this->encodeLabel = false;

        return $this;
    }

    /**
     * Whether to no allowed sorting by this column.
     *
     * @return $this
     */
    public function disableSorting(): self
    {
        $this->enableSorting = false;

        return $this;
    }

    /**
     * @param Closure|string|null $value an anonymous function or a string that is used to determine the value to
     * display in the current column. If this is an anonymous function, it will be called for each row and the return
     * value will be used as the value to display for every data model. The signature of this function should be:
     * `function ($model, $key, $index, $column)`. Where `$model`, `$key`, and `$index` refer to the model, key and
     * index of the row currently being rendered and `$column` is a reference to the {@see DataColumn} object. You may
     * also set this property to a string representing the attribute name to be displayed in this column. This can be
     * used when the attribute to be displayed is different from the {@see attribute} that is used for sorting and
     * filtering.
     *
     * If this is not set, `$model[$attribute]` will be used to obtain the value, where `$attribute` is the value of
     * {@see attribute}.
     *
     * @return $this
     */
    public function value($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param string the attribute name of the {@see filterModelName} associated with this column.
     *
     * If not set, will have the same value as {@see attribute}.
     *
     * @return $this
     */
    public function filterAttribute(string $filterAttribute): self
    {
        $this->filterAttribute = $filterAttribute;

        return $this;
    }

    protected function getHeaderCellLabel(): string
    {
        return $this->label === '' ? (new Inflector())->toHumanReadable($this->attribute) : $this->label;
    }

    protected function renderHeaderCellContent(): string
    {
        if ($this->header !== '' || ($this->label === '' && $this->attribute === '')) {
            return parent::renderHeaderCellContent();
        }

        $label = $this->getHeaderCellLabel();

        if ($this->encodeLabel) {
            $label = Html::encode($label);
        }

        $paginator = $this->grid->getPaginator();
        $sort = $paginator->getSort();

        if (
            $this->attribute !== '' &&
            $sort !== null &&
            isset($sort->getCriteria()[$this->attribute]) &&
            $this->enableSorting
        ) {
            return LinkSorter::widget()
                ->attribute($this->attribute)
                ->currentPage($this->grid->getPaginator()->getCurrentPage())
                ->frameworkCss($this->grid->getFrameworkCss())
                ->options(array_merge($this->sortLinkOptions, ['label' => $label]))
                ->requestAttributes($this->grid->getRequestAttributes())
                ->requestQueryParams($this->grid->getRequestQueryParams())
                ->sort($sort)
                ->render();
        }

        return $label;
    }

    protected function renderFilterCellContent(): string
    {
        if ($this->filter !== '') {
            return $this->filter;
        }

        if ($this->filterAttribute !== '') {
            if ($this->grid->getFrameworkCss() === 'bulma') {
                Html::AddCssClass($this->filterInputOptions, ['input' => 'input']);
            } else {
                Html::AddCssClass($this->filterInputOptions, ['input' => 'form-control']);
            }

            $name = $this->getInputName($this->grid->getFilterModelName(), $this->filterAttribute);

            return (string) Html::textInput($name, $this->filterValueDefault)->attributes($this->filterInputOptions);
        }

        return parent::renderFilterCellContent();
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

        return $this->getDataCellValue($model, $key, $index);
    }
}
