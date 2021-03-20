<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Closure;
use JsonException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Factory\Exceptions\InvalidConfigException;
use Yiisoft\Html\Html;
use Yiisoft\Json\Json;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\Columns\ActionColumn;
use Yiisoft\Yii\DataView\Columns\Column;
use Yiisoft\Yii\DataView\Columns\DataColumn;
use Yiisoft\Yii\DataView\Columns\RadioButtonColumn;
use Yiisoft\Yii\DataView\Factory\GridViewFactory;

use function array_filter;
use function array_keys;
use function array_merge;
use function call_user_func;
use function count;
use function implode;
use function is_array;
use function is_callable;
use function is_scalar;
use function is_string;
use function preg_match;
use function reset;

/**
 * The GridView widget is used to display data in a grid.
 *
 * It provides features like {@see sorter|sorting}, {@see pager|paging} and also {@see filterModelName|filtering}
 * the data.
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
 *
 * The look and feel of a grid view can be customized using the large amount of properties.
 *
 * For more details and usage information on GridView:
 * see the [guide article on data widgets](guide:output-data-widgets).
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
    private array $rowOptions = [];
    private ?Closure $beforeRow = null;
    private ?Closure $afterRow = null;
    private bool $showHeader = true;
    private bool $showFooter = false;
    private bool $placeFooterAfterBody = false;
    /** @var array<array-key,array<array-key,Column>|Column|string|callable|Closure> */
    private array $columns = [];
    private string $emptyCell = '&nbsp;';
    private string $filterModelName = '';
    private string $filterPosition = self::FILTER_POS_BODY;
    private array $filterRowOptions = ['class' => 'filters'];
    private GridViewFactory $gridViewFactory;

    public function __construct(GridViewFactory $gridViewFactory, TranslatorInterface $translator)
    {
        $this->gridViewFactory = $gridViewFactory;

        parent::__construct($translator);
    }

    protected function run(): string
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId() . '-gridview';
        }

        $this->initColumns();

        if (!isset($this->filterRowOptions['id'])) {
            $this->filterRowOptions['id'] = $this->getId() . '-filters';
        }

        return parent::run();
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getEmptyCell(): string
    {
        return $this->emptyCell;
    }

    public function getFilterModelName(): string
    {
        return $this->filterModelName;
    }

    public function isShowHeader(): bool
    {
        return $this->showHeader;
    }

    /**
     * @param Closure|null $afterRow an anonymous function that is called once AFTER rendering each data model.
     *
     * It should have the similar signature as {@see rowOptions}. The return result of the function will be rendered
     * directly.
     *
     * @return $this
     */
    public function afterRow(?Closure $afterRow): self
    {
        $new = clone $this;
        $new->afterRow = $afterRow;

        return $new;
    }

    /**
     * @param Closure|null $beforeRow an anonymous function that is called once BEFORE rendering each data model.
     *
     * It should have the similar signature as {@see rowOptions}. The return result of the function will be rendered
     * directly.
     *
     * @return $this
     */
    public function beforeRow(?Closure $beforeRow): self
    {
        $new = clone $this;
        $new->beforeRow = $beforeRow;

        return $new;
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
        $new = clone $this;
        $new->caption = $caption;

        return $new;
    }

    /**
     * @param array $captionOptions the HTML attributes for the caption element {@see caption}.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function captionOptions(array $captionOptions): self
    {
        $new = clone $this;
        $new->captionOptions = $captionOptions;

        return $new;
    }

    /**
     * @param array $columns grid column configuration. Each array element represents the configuration for one
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
     *     ['__class' => CheckboxColumn::class],
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
     *
     * Using the shortcut format the configuration for columns in simple cases would look like this:
     *
     * ```php
     * [
     *     'id',
     *     'amount:currency:Total Amount',
     *     'created_at:datetime',
     * ]
     * ```
     *
     * @psalm-param array<array-key,array<array-key,Column>|Column|string> $columns
     *
     * @return $this
     */
    public function columns(array $columns): self
    {
        $new = clone $this;
        $new->columns = $columns;

        return $new;
    }

    /**
     * @param string $dataColumnClass the default data column class if the class name is not explicitly specified when
     * configuring a data column. Defaults to 'DataColumn::class'.
     *
     * @return $this
     */
    public function dataColumnClass(string $dataColumnClass): self
    {
        $new = clone $this;
        $new->dataColumnClass = $dataColumnClass;

        return $new;
    }

    /**
     * @param string $emptyCell the HTML display when the content of a cell is empty. This property is used to render
     * cells that have no defined content, e.g. empty footer or filter cells.
     *
     * Note that this is not used by the {@see DataColumn} if a data item is `null`.
     *
     * @return $this
     */
    public function emptyCell(string $emptyCell): self
    {
        $new = clone $this;
        $new->emptyCell = $emptyCell;

        return $new;
    }

    /**
     * @param string the form model name that keeps the user-entered filter data. When this property is set, the grid
     * view will enable column-based filtering. Each data column by default will display a text field at the top that
     * users can fill in to filter the data.
     *
     * Note that in order to show an input field for filtering, a column must have its {@see DataColumn::attribute}
     * property set and the attribute should be active in the current scenario of $filterModelName or have
     * {@see DataColumn::filter} set as the HTML code for the input field.
     */
    public function filterModelName(string $filterModelName): self
    {
        $new = clone $this;
        $new->filterModelName = $filterModelName;

        return $new;
    }

    /**
     * @param string $filterPosition whether the filters should be displayed in the grid view.
     *
     * Valid values include:
     * - {@see FILTER_POS_HEADER}: the filters will be displayed on top of each column's header cell.
     * - {@see FILTER_POS_BODY}: the filters will be displayed right below each column's header cell.
     * - {@see FILTER_POS_FOOTER}: the filters will be displayed below each column's footer cell.
     *
     * @return $this
     */
    public function filterPosition(string $filterPosition): self
    {
        $new = clone $this;
        $new->filterPosition = $filterPosition;

        return $new;
    }

    /**
     * @param array $filterRowOptions the HTML attributes for the filter row element.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function filterRowOptions(array $filterRowOptions): self
    {
        $new = clone $this;
        $new->filterRowOptions = $filterRowOptions;

        return $new;
    }

    /**
     * @param array the HTML attributes for the table footer row.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function footerRowOptions(array $footerRowOptions): self
    {
        $new = clone $this;
        $new->footerRowOptions = $footerRowOptions;

        return $new;
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
        $new = clone $this;
        $new->headOptions = $headOptions;

        return $new;
    }

    /**
     * @param array $headerRowOptions the HTML attributes for the table header row.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function headerRowOptions(array $headerRowOptions): self
    {
        $new = clone $this;
        $new->headerRowOptions = $headerRowOptions;

        return $new;
    }

    /**
     * @param bool $value whether to place footer after body in DOM if is true.
     *
     * @return $this
     */
    public function placeFooterAfterBody(bool $value): self
    {
        $new = clone $this;
        $new->placeFooterAfterBody = $value;

        return $new;
    }

    /**
     * @param array $rowOptions the HTML attributes for the table body rows. This can be either an array specifying the
     * common HTML attributes for all body rows.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function rowOptions($rowOptions): self
    {
        $new = clone $this;
        $new->rowOptions = $rowOptions;

        return $new;
    }

    /**
     * @param bool $showFooter whether to show the footer section of the grid table.
     *
     * @return $this
     */
    public function showFooter(): self
    {
        $new = clone $this;
        $new->showFooter = true;

        return $new;
    }

    /**
     * @param bool $showHeader whether to show the header section of the grid table.
     *
     * @return $this
     */
    public function showHeader(bool $showHeader): self
    {
        $new = clone $this;
        $new->showHeader = $showHeader;

        return $new;
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
        $new = clone $this;
        $new->tableOptions = ArrayHelper::merge($this->tableOptions, $tableOptions);

        return $new;
    }

    /**
     * Renders the data models for the grid view.
     *
     * @throws JsonException
     *
     * @return string the HTML code of table
     */
    protected function renderItems(): string
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

        return Html::tag('table', implode("\n", $content), $this->tableOptions)->encode(false)->render();
    }

    /**
     * Creates a {@see DataColumn} object based on a string in the format of "attribute:format:label".
     *
     * @param string $text the column specification string.
     *
     * @throws InvalidConfigException if the column specification is invalid.
     *
     * @return Column the column instance.
     */
    private function createDataColumn(string $text): Column
    {
        if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $text, $matches)) {
            throw new InvalidConfigException(
                'The column must be specified in the format of "attribute", "attribute:format" or ' .
                '"attribute:format:label"'
            );
        }

        $config = [
            '__class' => DataColumn::class,
            'attribute()' => [$matches[1]],
            'label()' => [$matches[5] ?? ''],
            'grid()' => [$this],
        ];

        return $this->gridViewFactory->createColumnClass($config);
    }

    /**
     * This function tries to guess the columns to show from the given data if {@see columns} are not explicitly
     * specified.
     */
    private function guessColumns(): void
    {
        $models = $this->getDataReader();

        reset($models);

        /** @var array<array-key,object|int|bool|string|null> $models */
        foreach ($models as $name => $value) {
            if ($value === null || is_scalar($value) || is_callable([$value, '__toString'])) {
                $this->columns[] = (string) $name;
            }
        }
    }

    /**
     * Creates column objects and initializes them.
     *
     * @throws InvalidConfigException
     */
    private function initColumns(): void
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }

        foreach ($this->columns as $i => $column) {
            if (is_string($column)) {
                $column = $this->createDataColumn($column);
            } elseif (is_array($column)) {
                $buttons = null;
                $content = null;
                $value = null;
                $radioOptions = null;

                if (isset($column['buttons'])) {
                    /** @var array */
                    $buttons = $column['buttons'];
                    unset($column['buttons']);
                }

                if (isset($column['content']) && is_callable($column['content'])) {
                    /** @var callable|null */
                    $content = $column['content'];
                    unset($column['content']);
                }

                if (isset($column['radioOptions']) && $column['radioOptions'] instanceof Closure) {
                    /** @var Closure|null */
                    $radioOptions = $column['radioOptions'];
                    unset($column['radioOptions']);
                }

                if (isset($column['value']) && $column['value'] instanceof Closure) {
                    /** @var Closure|null */
                    $value = $column['value'];
                    unset($column['value']);
                }

                $config = array_merge(
                    [
                        '__class' => $this->dataColumnClass,
                        'grid()' => [$this],
                    ],
                    $column,
                );

                $column = $this->gridViewFactory->createColumnClass($config);

                if ($column instanceof ActionColumn && $buttons !== null) {
                    $column->buttons($buttons);
                }

                if ($content !== null) {
                    $column->content($content);
                }

                if ($column instanceof RadioButtonColumn && $radioOptions !== null) {
                    $column->radioOptions($radioOptions);
                }

                if ($column instanceof DataColumn && $value !== null) {
                    $column->value($value);
                }
            }

            if ($column instanceof Column && !$column->isVisible()) {
                unset($this->columns[$i]);
                continue;
            }

            $this->columns[$i] = $column;
        }
    }

    /**
     * Renders the caption element.
     *
     * @throws JsonException
     *
     * @return string|null ?string the rendered caption element or `false` if no caption element should be rendered.
     */
    private function renderCaption(): ?string
    {
        if (!empty($this->caption)) {
            return Html::tag('caption', $this->caption, $this->captionOptions)->render();
        }

        return null;
    }

    /**
     * Renders the column group HTML.
     *
     * @throws JsonException
     *
     * @return bool|string the column group HTML or `false` if no column group should be rendered.
     */
    private function renderColumnGroup()
    {
        foreach ($this->columns as $column) {
            if ($column instanceof Column && !empty($column->getOptions())) {
                $cols = [];
                foreach ($this->columns as $col) {
                    if ($col instanceof Column) {
                        $cols[] = Html::tag('col', '', $col->getOptions())->render();
                    }
                }

                return Html::tag('colgroup', implode("\n", $cols))->render();
            }
        }

        return false;
    }

    /**
     * Renders the filter.
     *
     * @throws JsonException
     *
     * @return string the rendering result.
     */
    private function renderFilters(): string
    {
        if ($this->getFilterModelName() !== '') {
            $cells = [];

            foreach ($this->columns as $column) {
                if ($column instanceof Column) {
                    $cells[] = $column->renderFilterCell();
                }
            }

            return Html::tag('tr', implode('', $cells), $this->filterRowOptions)->encode(false)->render();
        }

        return '';
    }

    /**
     * Renders the table body.
     *
     * @throws JsonException
     *
     * @return string the rendering result.
     */
    private function renderTableBody(): string
    {
        $models = $this->getDataReader();
        $keys = array_keys($models);
        $rows = [];

        /** @var array<int,array> $models */
        foreach ($models as $index => $model) {
            $key = $keys[$index];

            if ($this->beforeRow !== null) {
                /** @var array */
                $row = call_user_func($this->beforeRow, $model, $key, $index, $this);

                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $rows[] = $this->renderTableRow($model, $key, $index);

            if ($this->afterRow !== null) {
                /** @var array */
                $row = call_user_func($this->afterRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }
        }

        if (empty($rows) && $this->emptyText !== '') {
            $colspan = count($this->columns);

            return "<tbody>\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>\n";
        }

        return "<tbody>\n" . implode("\n", $rows) . "\n</tbody>\n";
    }

    /**
     * Renders the table footer.
     *
     * @throws JsonException
     *
     * @return string the rendering result.
     */
    private function renderTableFooter(): string
    {
        $cells = [];

        foreach ($this->columns as $column) {
            if ($column instanceof Column) {
                $cells[] = $column->renderFooterCell();
            }
        }

        $content = Html::tag('tr', implode('', $cells), $this->footerRowOptions)->encode(false)->render();

        if ($this->filterPosition === self::FILTER_POS_FOOTER) {
            $content .= $this->renderFilters();
        }

        return "<tfoot>\n" . $content . "\n</tfoot>";
    }

    /**
     * Renders the table header.
     *
     * @throws JsonException
     *
     * @return string the rendering result.
     */
    private function renderTableHeader(): string
    {
        $cells = [];

        foreach ($this->columns as $column) {
            if ($column instanceof Column) {
                $cells[] = $column->renderHeaderCell();
            }
        }

        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions)->encode(false)->render();

        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            $content = $this->renderFilters() . $content;
        } elseif ($this->filterPosition === self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }

        return "\n" . Html::tag('thead', "\n$content\n", $this->headOptions)->encode(false)->render();
    }

    /**
     * Renders a table row with the given data model and key.
     *
     * @param array|object $model the data model to be rendered
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the model array returned by {@see dataReader}.
     *
     * @throws JsonException
     *
     * @return string the rendering result
     */
    private function renderTableRow($model, $key, int $index): string
    {
        $cells = [];

        foreach ($this->columns as $column) {
            if ($column instanceof Column) {
                $cells[] = $column->renderDataCell($model, $key, $index);
            }
        }

        $this->rowOptions['data-key'] = is_array($key) ? Json::encode($key) : (string)$key;

        return Html::tag('tr', implode('', $cells), $this->rowOptions)->encode(false)->render();
    }
}
