<?php

declare(strict_types=1);

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
 *
 * It provides features like {@see sorter|sorting}, {@see pager|paging} and also {@see filterModel|filtering} the data.
 *
 * A basic usage looks like the following:
 * ```php
 * <?= GridView::widget()
 *     ->columns($columns)
 *     ->linkPagerClass(LinkPager::class)
 *     ->run()
 * ?>
 * ```
 *
 * The columns of the grid table are configured in terms of {@see Column} classes, which are configured via
 * {@see columns}.
 * The look and feel of a grid view can be customized using the large amount of properties.
 * For more details and usage information on GridView, see the [guide article on data
 * widgets](guide:output-data-widgets).
 */
final class GridView extends BaseListView
{
    public const FILTER_POS_HEADER = 'header';
    public const FILTER_POS_FOOTER = 'footer';
    public const FILTER_POS_BODY = 'body';
    protected array $options = ['class' => 'grid-view'];
    protected bool $showOnEmpty = true;
    private string $dataColumnClass = DataColumn::class;
    private string $caption = '';
    private array $captionOptions = [];
    private array $tableOptions = ['class' => 'table table-striped table-bordered'];
    private array $headOptions = [];
    private array $headerRowOptions = [];
    private array $footerRowOptions = [];
    private $rowOptions = [];
    private ?Closure $beforeRow = null;
    private ?Closure $afterRow = null;
    private bool $showHeader = true;
    private bool $showFooter = false;
    private bool $placeFooterAfterBody = false;
    private array $columns = [];

    /**
     * @var string the HTML display when the content of a cell is empty.
     *             This property is used to render cells that have no defined content,
     *             e.g. empty footer or filter cells.
     * Note that this is not used by the [[DataColumn]] if a data item is `null`.
     */
    private string $emptyCell = '&nbsp;';

    /**
     * @var object|null the model that keeps the user-entered filter data. When this property is set,
     *                      the grid view will enable column-based filtering. Each data column by default will display
     *     a text field at the top that users can fill in to filter the data. Note that in order to show an input field
     *     for filtering, a column must have its [[DataColumn::attribute]] property set and the attribute should be
     *     active in the current scenario of $filterModel or have
     * [[DataColumn::filter]] set as the HTML code for the input field.
     * When this property is not set (null) the filtering feature is disabled.
     */
    private ?object $filterModel = null;

    /**
     * @var string whether the filters should be displayed in the grid view. Valid values include:
     * - [[FILTER_POS_HEADER]]: the filters will be displayed on top of each column's header cell.
     * - [[FILTER_POS_BODY]]: the filters will be displayed right below each column's header cell.
     * - [[FILTER_POS_FOOTER]]: the filters will be displayed below each column's footer cell.
     */
    private string $filterPosition = self::FILTER_POS_BODY;

    /**
     * @var array the HTML attributes for the filter row element.
     *
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    private array $filterRowOptions = ['class' => 'filters'];

    protected function init(): void
    {
        if (!isset($this->filterRowOptions['id'])) {
            $this->filterRowOptions['id'] = ($this->options['id'] ?? $this->getId()) . '-filters';
        }

        $this->initColumns();
    }

    /**
     * Renders the data models for the grid view.
     *
     * @throws \JsonException
     *
     * @return string the HTML code of table
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
     * @return string|null ?string the rendered caption element or `false` if no caption element should be rendered.
     */
    public function renderCaption(): ?string
    {
        if (!empty($this->caption)) {
            return Html::tag('caption', $this->caption, $this->captionOptions);
        }

        return null;
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
            if (!empty($column->getOptions())) {
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
    public function renderTableHeader(): string
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
    public function renderTableFooter(): string
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
    public function renderFilters(): string
    {
        if ($this->getFilterModel() !== null) {
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
     * @throws \JsonException
     *
     * @return string the rendering result.
     */
    public function renderTableBody(): string
    {
        $models = $this->getDataReader();
        $keys = array_keys($models);
        $rows = [];

        foreach ($models as $index => $model) {
            $key = $keys[$index];
            if ($this->beforeRow !== null) {
                $row = \call_user_func($this->beforeRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $rows[] = $this->renderTableRow($model, $key, $index);

            if ($this->afterRow !== null) {
                $row = \call_user_func($this->afterRow, $model, $key, $index, $this);
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
     *
     * @throws \JsonException
     *
     * @return string the rendering result
     */
    public function renderTableRow($model, $key, $index): string
    {
        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }
        if ($this->rowOptions instanceof Closure) {
            $options = \call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $options['data-key'] = \is_array($key) ? Json::encode($key) : (string)$key;

        return Html::tag('tr', implode('', $cells), $options);
    }

    /**
     * Creates column objects and initializes them.
     *
     * @throws \Yiisoft\Factory\Exceptions\InvalidConfigException
     */
    protected function initColumns(): void
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }
        foreach ($this->columns as $i => $column) {
            if (\is_string($column)) {
                $column = $this->createDataColumn($column);
            } else {
                $buttons = null;
                $value = null;

                if (isset($column['buttons()'])) {
                    $buttons = $column['buttons()'];
                    unset($column['buttons()']);
                }

                if (isset($column['value()'])) {
                    $value = $column['value()'];
                    unset($column['value()']);
                }

                $config = array_merge(
                    [
                        '__class' => $this->dataColumnClass,
                        'grid()' => [$this],
                    ],
                    $column,
                );

                $column = $this->gridViewFactory->createColumnClass($config);

                if ($buttons !== null) {
                    $column->buttons($buttons);
                }

                if ($value !== null) {
                    $column->value($value);
                }
            }

            if (!$column->isVisible()) {
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
     *
     * @throws InvalidConfigException if the column specification is invalid
     *
     * @return DataColumn the column instance
     */
    protected function createDataColumn($text): DataColumn
    {
        if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $text, $matches)) {
            throw new InvalidConfigException(
                'The column must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"'
            );
        }

        /* @var DataColumn $widget */
        $widget = ($this->dataColumnClass)::widget();

        return $widget->grid($this)
            ->attribute($matches[1])
            ->format($matches[3] ?? 'text')
            ->label($matches[5] ?? null);
    }

    /**
     * This function tries to guess the columns to show from the given data
     * if [[columns]] are not explicitly specified.
     */
    protected function guessColumns(): void
    {
        $models = $this->getDataReader();
        $model = reset($models);

        if (\is_array($model) || \is_object($model)) {
            foreach ($this->paginator->read() as $name => $value) {
                if ($value === null || is_scalar($value) || \is_callable([$value, '__toString'])) {
                    $this->columns[] = (string)$name;
                }
            }
        }
    }

    /**
     * @param bool $showHeader whether to show the header section of the grid table.
     *
     * @return $this
     */
    public function showHeader(bool $showHeader): self
    {
        $this->showHeader = $showHeader;

        return $this;
    }

    /**
     * @param array $tableOptions the HTML attributes for the grid table element.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function tableOptions(array $tableOptions): self
    {
        $this->tableOptions = ArrayHelper::merge($this->tableOptions, $tableOptions);

        return $this;
    }

    /**
     * @param bool $showFooter whether to show the footer section of the grid table.
     *
     * @return $this
     */
    public function showFooter(bool $value): self
    {
        $this->showFooter = $value;

        return $this;
    }

    /**
     * @param bool $value whether to place footer after body in DOM if is true.
     *
     * @return $this
     */
    public function placeFooterAfterBody(bool $value): self
    {
        $this->placeFooterAfterBody = $value;

        return $this;
    }

    /**
     * @param array $column grid column configuration. Each array element represents the configuration for one
     * particular grid column. For example,
     *
     * ```php
     * [
     *     ['__class' => SerialColumn::class],
     *     [
     *         '__class' => DataColumn::class, // this line is optional
     *         'attribute()' => ['name'],
     *         'format()' => ['text'],
     *         'label()' => ['Name'],
     *     ],
     *     ['__class' => \yii\grid\CheckboxColumn::class],
     * ]
     * ```
     *
     * If a column is of class {@see DataColumn}, the "class" element can be omitted.
     *
     * As a shortcut format, a string may be used to specify the configuration of a data column which only contains
     * {@see DataColumn::attribute|attribute}, {@see DataColumn::format|format}, and/or {@see DataColumn::label|label}
     * options: `"attribute:format:label"`.
     *
     * For example, the above "name" column can also be specified as: `"name:text:Name"`.
     *
     * Both "format" and "label" are optional. They will take default values if absent.
     * Using the shortcut format the configuration for columns in simple cases would look like this:
     *
     * ```php
     * [
     *     'id',
     *     'amount:currency:Total Amount',
     *     'created_at:datetime',
     * ]
     * ```
     */
    public function columns(array $columns): self
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

    public function isShowHeader(): bool
    {
        return $this->showHeader;
    }

    public function getEmptyCell(): string
    {
        return $this->emptyCell;
    }

    public function getFilterModel(): ?object
    {
        return $this->filterModel;
    }

    public function getId(): string
    {
        return 'gridview-widget-1';
    }

    private function getDataReader(): array
    {
        $dataReader = [];

        foreach ($this->paginator->read() as $read) {
            $dataReader[] = $read;
        }

        return $dataReader;
    }

    /**
     * @param string $dataColumnClass the default data column class if the class name is not explicitly specified when
     * configuring a data column. Defaults to 'DataColumn::class'.
     *
     * @return $this
     */
    public function dataColumnClass(string $dataColumnClass): self
    {
        $this->dataColumnClass = $dataColumnClass;

        return $this;
    }

    /**
     * @param string $caption the caption of the grid table
     *
     * @return $this
     *
     * @see captionOptions
     */
    public function caption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * @param array $captionOptions the HTML attributes for the caption element.
     *
     * @return $this
     *
     * @see caption
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function captionOptions(array $captionOptions): self
    {
        $this->captionOptions = $captionOptions;

        return $this;
    }

    /**
     * @param array the HTML attributes for the grid thead element.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function headOptions(array $headOptions): self
    {
        $this->headOptions = $headOptions;

        return $this;
    }

    /**
     * @param array $headerRowOptions the HTML attributes for the table header row.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function headerRowOptions($headerRowOptions): self
    {
        $this->headerRowOptions = $headerRowOptions;

        return $this;
    }

    /**
     * @param array the HTML attributes for the table footer row.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function footerRowOptions($footerRowOptions)
    {
        $this->footerRowOptions = $footerRowOptions;

        return $this;
    }

    /**
     * @param array|Closure $rowOptions the HTML attributes for the table body rows. This can be either an array
     * specifying the common HTML attributes for all body rows, or an anonymous function that returns an array of
     * the HTML attributes. The anonymous function will be called once for every data model returned by
     * {@see dataProvider}. It should have the following signature:
     *
     * ```php
     * function ($model, $key, $index, $grid)
     * ```
     * - `$model`: the current data model being rendered
     * - `$key`: the key value associated with the current data model
     * - `$index`: the zero-based index of the data model in the model array returned by {@see dataReader]]
     * - `$grid`: the GridView object
     *
     * @return $this
     *
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public function rowOptions($rowOptions): self
    {
        $this->rowOptions = $rowOptions;

        return $this;
    }

    /**
     * @param Closure|null $beforeRow an anonymous function that is called once BEFORE rendering each data model.
     * It should have the similar signature as {@see rowOptions}. The return result of the function will be rendered
     * directly.
     *
     * @return $this
     */
    public function beforeRow($beforeRow): self
    {
        $this->beforeRow = $beforeRow;

        return $this;
    }

    /**
     * @param Closure|null $afterRow an anonymous function that is called once AFTER rendering each data model.
     *
     * It should have the similar signature as {@see rowOptions}. The return result of the function will be rendered
     * directly.
     *
     * @return $this
     */
    public function afterRow($afterRow): self
    {
        $this->afterRow = $afterRow;

        return $this;
    }

    public function pageSize(int $pageSize): self
    {
        $this->paginator = $this->paginator->withPageSize($pageSize);

        return $this;
    }

    public function currentPage(int $currentPage): self
    {
        $this->paginator = $this->paginator->withCurrentPage($currentPage);

        return $this;
    }
}
