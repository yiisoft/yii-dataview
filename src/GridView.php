<?php

namespace Yiisoft\Yii\DataView;

use Closure;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Factory\Exceptions\InvalidConfigException;
use Yiisoft\Html\Html;
use Yiisoft\I18n\MessageFormatterInterface;
use Yiisoft\Json\Json;
use Yiisoft\Yii\DataView\Columns\Column;
use Yiisoft\Yii\DataView\Columns\DataColumn;

/**
 * The GridView widget is used to display data in a grid.
 * It provides features like [[sorter|sorting]], [[pager|paging]] and also [[filterModel|filtering]] the data.
 * A basic usage looks like the following:
 * ```php
 * <?= GridView::widget([
 *     'dataProvider' => $dataProvider,
 *     'columns' => [
 *         'id',
 *         'name',
 *         'created_at:datetime',
 *         // ...
 *     ],
 * ]) ?>
 * ```
 * The columns of the grid table are configured in terms of [[Column]] classes,
 * which are configured via [[columns]].
 * The look and feel of a grid view can be customized using the large amount of properties.
 * For more details and usage information on GridView, see the [guide article on data
 * widgets](guide:output-data-widgets).
 */
class GridView extends BaseListView
{
    const FILTER_POS_HEADER = 'header';
    const FILTER_POS_FOOTER = 'footer';
    const FILTER_POS_BODY = 'body';

    /**
     * @var string the default data column class if the class name is not explicitly specified when configuring a data
     *     column. Defaults to 'yii\grid\DataColumn'.
     */
    public string $dataColumnClass = DataColumn::class;
    /**
     * @var string the caption of the grid table
     * @see captionOptions
     */
    public $caption;
    /**
     * @var array the HTML attributes for the caption element.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     * @see caption
     */
    public array $captionOptions = [];
    /**
     * @var array the HTML attributes for the grid table element.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public array $tableOptions = ['class' => 'table table-striped table-bordered'];
    /**
     * @var array the HTML attributes for the grid thead element.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public array $headOptions = [];
    /**
     * @var array the HTML attributes for the container tag of the grid view.
     *            The "tag" element specifies the tag name of the container element and defaults to "div".
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public array $options = ['class' => 'grid-view'];
    /**
     * @var array the HTML attributes for the table header row.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public array $headerRowOptions = [];
    /**
     * @var array the HTML attributes for the table footer row.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public array $footerRowOptions = [];
    /**
     * @var array|Closure the HTML attributes for the table body rows. This can be either an array
     *                    specifying the common HTML attributes for all body rows, or an anonymous function that
     *                    returns an array of the HTML attributes. The anonymous function will be called once for every
     *                    data model returned by [[dataProvider]]. It should have the following signature:
     * ```php
     * function ($model, $key, $index, $grid)
     * ```
     * - `$model`: the current data model being rendered
     * - `$key`: the key value associated with the current data model
     * - `$index`: the zero-based index of the data model in the model array returned by [[dataProvider]]
     * - `$grid`: the GridView object
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $rowOptions = [];
    /**
     * @var Closure an anonymous function that is called once BEFORE rendering each data model.
     *              It should have the similar signature as [[rowOptions]]. The return result of the function
     *              will be rendered directly.
     */
    public $beforeRow;
    /**
     * @var Closure an anonymous function that is called once AFTER rendering each data model.
     *              It should have the similar signature as [[rowOptions]]. The return result of the function
     *              will be rendered directly.
     */
    public $afterRow;
    /**
     * @var bool whether to show the header section of the grid table.
     */
    public $showHeader = true;
    /**
     * @var bool whether to show the footer section of the grid table.
     */
    public $showFooter = false;
    /**
     * @var bool whether to place footer after body in DOM if is true
     * @since 2.0.14
     */
    public $placeFooterAfterBody = false;
    /**
     * @var bool whether to show the grid view if [[dataProvider]] returns no data.
     */
    public $showOnEmpty = true;
    /**
     * @var \Yiisoft\I18n\MessageFormatterInterface the formatter used to format model attribute values into
     *     displayable texts.
     */
    protected MessageFormatterInterface $messageFormatter;
    /**
     * @var array grid column configuration. Each array element represents the configuration
     *            for one particular grid column. For example,
     * ```php
     * [
     *     ['__class' => \yii\grid\SerialColumn::class],
     *     [
     *         '__class' => \yii\grid\DataColumn::class, // this line is optional
     *         'attribute' => 'name',
     *         'format' => 'text',
     *         'label' => 'Name',
     *     ],
     *     ['__class' => \yii\grid\CheckboxColumn::class],
     * ]
     * ```
     * If a column is of class [[DataColumn]], the "class" element can be omitted.
     * As a shortcut format, a string may be used to specify the configuration of a data column
     * which only contains [[DataColumn::attribute|attribute]], [[DataColumn::format|format]],
     * and/or [[DataColumn::label|label]] options: `"attribute:format:label"`.
     * For example, the above "name" column can also be specified as: `"name:text:Name"`.
     * Both "format" and "label" are optional. They will take default values if absent.
     * Using the shortcut format the configuration for columns in simple cases would look like this:
     * ```php
     * [
     *     'id',
     *     'amount:currency:Total Amount',
     *     'created_at:datetime',
     * ]
     * ```
     * When using a [[dataProvider]] with active records, you can also display values from related records,
     * e.g. the `name` attribute of the `author` relation:
     * ```php
     * // shortcut syntax
     * 'author.name',
     * // full syntax
     * [
     *     'attribute' => 'author.name',
     *     // ...
     * ]
     * ```
     */
    protected array $columns = [];
    /**
     * @var string the HTML display when the content of a cell is empty.
     *             This property is used to render cells that have no defined content,
     *             e.g. empty footer or filter cells.
     * Note that this is not used by the [[DataColumn]] if a data item is `null`.
     */
    public string $emptyCell = '&nbsp;';
    /**
     * @var \yii\base\Model the model that keeps the user-entered filter data. When this property is set,
     *                      the grid view will enable column-based filtering. Each data column by default will display
     *     a text field at the top that users can fill in to filter the data. Note that in order to show an input field
     *     for filtering, a column must have its [[DataColumn::attribute]] property set and the attribute should be
     *     active in the current scenario of $filterModel or have
     * [[DataColumn::filter]] set as the HTML code for the input field.
     * When this property is not set (null) the filtering feature is disabled.
     */
    public $filterModel;
    /**
     * TODO not used
     *
     * @var string|array the URL for returning the filtering result. [[Url::to()]] will be called to
     *                   normalize the URL. If not set, the current controller action will be used.
     *                   When the user makes change to any filter input, the current filtering inputs will be appended
     *                   as GET parameters to this URL.
     */
    public $filterUrl;
    /**
     * @var string whether the filters should be displayed in the grid view. Valid values include:
     * - [[FILTER_POS_HEADER]]: the filters will be displayed on top of each column's header cell.
     * - [[FILTER_POS_BODY]]: the filters will be displayed right below each column's header cell.
     * - [[FILTER_POS_FOOTER]]: the filters will be displayed below each column's footer cell.
     */
    public string $filterPosition = self::FILTER_POS_BODY;
    /**
     * @var array the HTML attributes for the filter row element.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public array $filterRowOptions = ['class' => 'filters'];
    /**
     * @var array the options for rendering the filter error summary.
     *            Please refer to [[Html::errorSummary()]] for more details about how to specify the options.
     * @see renderErrors()
     */
    public array $filterErrorSummaryOptions = ['class' => 'error-summary'];
    /**
     * @var array the options for rendering every filter error message.
     *            This is mainly used by [[Html::error()]] when rendering an error message next to every filter input
     *     field.
     */
    public array $filterErrorOptions = ['class' => 'help-block'];
    /**
     * TODO not used
     * @var string the layout that determines how different sections of the grid view should be organized.
     *             The following tokens will be replaced with the corresponding section contents:
     * - `{summary}`: the summary section. See [[renderSummary()]].
     * - `{errors}`: the filter model error summary. See [[renderErrors()]].
     * - `{items}`: the list items. See [[renderItems()]].
     * - `{sorter}`: the sorter. See [[renderSorter()]].
     * - `{pager}`: the pager. See [[renderPager()]].
     */
    public string $layout = "{summary}\n{items}\n{pager}";

    /**
     * Initializes the grid view.
     * This method will initialize required property values and instantiate [[columns]] objects.
     *
     * @throws \Yiisoft\Factory\Exceptions\InvalidConfigException
     */
    public function init(): self
    {
        parent::init();
        if (!isset($this->filterRowOptions['id'])) {
            $this->filterRowOptions['id'] = ($this->options['id'] ?? $this->getId()) . '-filters';
        }

        $this->initColumns();

        return $this;
    }

    /**
     * Renders validator errors of filter model.
     *
     * @return string the rendering result.
     */
    public function renderErrors(): string
    {
        if ($this->filterModel instanceof Model && $this->filterModel->hasErrors()) {
            return Html::errorSummary($this->filterModel, $this->filterErrorSummaryOptions);
        }

        return '';
    }

    public function renderSection($name): string
    {
        switch ($name) {
            case '{errors}':
                return $this->renderErrors();
            default:
                return parent::renderSection($name);
        }
    }

    /**
     * Renders the data models for the grid view.
     *
     * @return string the HTML code of table
     * @throws \JsonException
     */
    public function renderItems(): string
    {
        $caption = $this->renderCaption();
        $columnGroup = $this->renderColumnGroup();
        $tableHeader = $this->showHeader ? $this->renderTableHeader() : false;
        $tableBody = $this->renderTableBody();

        $tableFooter = false;
        $tableFooterAfterBody = false;

        if ($this->showFooter) {
            if ($this->placeFooterAfterBody) {
                $tableFooterAfterBody = $this->renderTableFooter();
            } else {
                $tableFooter = $this->renderTableFooter();
            }
        }

        $content = array_filter(
            [
                $caption,
                $columnGroup,
                $tableHeader,
                $tableFooter,
                $tableBody,
                $tableFooterAfterBody,
            ]
        );

        return Html::tag('table', implode("\n", $content), $this->tableOptions);
    }

    /**
     * Renders the caption element.
     *
     * @return bool|string the rendered caption element or `false` if no caption element should be rendered.
     */
    public function renderCaption()
    {
        if (!empty($this->caption)) {
            return Html::tag('caption', $this->caption, $this->captionOptions);
        }

        return false;
    }

    /**
     * Renders the column group HTML.
     *
     * @return bool|string the column group HTML or `false` if no column group should be rendered.
     */
    public function renderColumnGroup()
    {
        foreach ($this->columns as $column) {
            /* @var $column Column */
            if (!empty($column->options)) {
                $cols = [];
                foreach ($this->columns as $col) {
                    $cols[] = Html::tag('col', '', $col->options);
                }

                return Html::tag('colgroup', implode("\n", $cols));
            }
        }

        return false;
    }

    /**
     * Renders the table header.
     *
     * @return string the rendering result.
     */
    public function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderHeaderCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            $content = $this->renderFilters() . $content;
        } elseif ($this->filterPosition === self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }

        return Html::tag('thead', "\n$content\n", $this->headOptions);
    }

    /**
     * Renders the table footer.
     *
     * @return string the rendering result.
     */
    public function renderTableFooter()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderFooterCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->footerRowOptions);
        if ($this->filterPosition === self::FILTER_POS_FOOTER) {
            $content .= $this->renderFilters();
        }

        return "<tfoot>\n" . $content . "\n</tfoot>";
    }

    /**
     * Renders the filter.
     *
     * @return string the rendering result.
     */
    public function renderFilters()
    {
        if ($this->filterModel !== null) {
            $cells = [];
            foreach ($this->columns as $column) {
                /* @var $column Column */
                $cells[] = $column->renderFilterCell();
            }

            return Html::tag('tr', implode('', $cells), $this->filterRowOptions);
        }

        return '';
    }

    /**
     * Renders the table body.
     *
     * @return string the rendering result.
     * @throws \JsonException
     */
    public function renderTableBody()
    {
        $models = $this->dataReader->read();
        $keys = array_keys($models);
        $rows = [];
        foreach ($models as $index => $model) {
            $key = $keys[$index];
            if ($this->beforeRow !== null) {
                $row = call_user_func($this->beforeRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $rows[] = $this->renderTableRow($model, $key, $index);

            if ($this->afterRow !== null) {
                $row = call_user_func($this->afterRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }
        }

        if (empty($rows) && $this->emptyText !== null) {
            $colspan = count($this->columns);

            return "<tbody>\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
        }

        return "<tbody>\n" . implode("\n", $rows) . "\n</tbody>";
    }

    /**
     * Renders a table row with the given data model and key.
     *
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the model array returned by [[dataProvider]].
     * @return string the rendering result
     * @throws \JsonException
     */
    public function renderTableRow($model, $key, $index)
    {
        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }
        if ($this->rowOptions instanceof Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $options['data-key'] = is_array($key) ? Json::encode($key) : (string)$key;

        return Html::tag('tr', implode('', $cells), $options);
    }

    /**
     * Creates column objects and initializes them.
     */
    protected function initColumns()
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }
        foreach ($this->columns as $i => $column) {
            if (is_string($column)) {
                $column = $this->createDataColumn($column);
            } else {
                $column = $this->dataColumnClass::widget()
                    ->grid($this);
            }
            if (!$column->visible) {
                unset($this->columns[$i]);
                continue;
            }
            $this->columns[$i] = $column;
        }
    }

    /**
     * Creates a [[DataColumn]] object based on a string in the format of "attribute:format:label".
     *
     * @param string $text the column specification string
     * @return DataColumn the column instance
     * @throws InvalidConfigException if the column specification is invalid
     */
    protected function createDataColumn($text)
    {
        if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $text, $matches)) {
            throw new InvalidConfigException(
                'The column must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"'
            );
        }

        /* @var DataColumn $widget */
        $widget = ($this->dataColumnClass)::widget();

        return $widget->withGrid($this)
            ->withAttribute($matches[1])
            ->withFormat($matches[3] ?? 'text')
            ->withLabel($matches[5] ?? null);
    }

    /**
     * This function tries to guess the columns to show from the given data
     * if [[columns]] are not explicitly specified.
     */
    protected function guessColumns()
    {
        $models = $this->dataReader->read();
        $model = reset($models);
        if (is_array($model) || is_object($model)) {
            foreach ($model as $name => $value) {
                if ($value === null || is_scalar($value) || is_callable([$value, '__toString'])) {
                    $this->columns[] = (string)$name;
                }
            }
        }
    }

    /**
     * @param bool $showHeader
     * @return self
     */
    public function withShowHeader(bool $showHeader): self
    {
        $this->showHeader = $showHeader;

        return $this;
    }

    /**
     * @param array $tableOptions
     * @return GridView
     */
    public function withTableOptions(array $tableOptions): self
    {
        $this->tableOptions = ArrayHelper::merge($this->tableOptions, $tableOptions);

        return $this;
    }

    public function withShowFooter(bool $value): self
    {
        $this->showFooter = $value;

        return $this;
    }

    public function withPlaceFooterAfterBody(bool $value): self
    {
        $this->placeFooterAfterBody = $value;

        return $this;
    }

    public function withColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getMessageFormatter(): MessageFormatterInterface
    {
        return $this->messageFormatter;
    }
}
