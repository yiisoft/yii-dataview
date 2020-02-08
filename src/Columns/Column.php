<?php

namespace Yiisoft\Yii\DataView\Columns;

use Closure;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\MessageFormatter;

/**
 * Column is the base class of all [[GridView]] column classes.
 * For more details and usage information on Column, see the [guide article on data widgets](guide:output-data-widgets).
 */
abstract class Column extends Widget
{
    /**
     * @var GridView the grid view object that owns this column.
     */
    protected GridView $grid;
    /**
     * @var string the header cell content. Note that it will not be HTML-encoded.
     */
    protected ?string $header = null;
    /**
     * @var string the footer cell content. Note that it will not be HTML-encoded.
     */
    private string $footer = '';
    /**
     * @var callable This is a callable that will be used to generate the content of each cell.
     *               The signature of the function should be the following: `function ($model, $key, $index, $column)`.
     *               Where `$model`, `$key`, and `$index` refer to the model, key and index of the row currently being
     *     rendered and `$column` is a reference to the [[Column]] object.
     */
    protected $content;
    /**
     * @var bool whether this column is visible. Defaults to true.
     */
    protected bool $visible = true;
    /**
     * @var array the HTML attributes for the column group tag.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    protected array $options = [];
    /**
     * @var array the HTML attributes for the header cell tag.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    protected array $headerOptions = [];
    /**
     * @var array|\Closure the HTML attributes for the data cell tag. This can either be an array of
     *                     attributes or an anonymous function ([[Closure]]) that returns such an array.
     *                     The signature of the function should be the following: `function ($model, $key, $index,
     *     $column)`. Where `$model`, `$key`, and `$index` refer to the model, key and index of the row currently being
     *     rendered and `$column` is a reference to the [[Column]] object. A function may be used to assign different
     *     attributes to different rows based on the data in that row.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    protected $contentOptions = [];
    /**
     * @var array the HTML attributes for the footer cell tag.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    protected array $footerOptions = [];
    /**
     * @var array the HTML attributes for the filter cell tag.
     * @see \Yiisoft\Html\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    protected array $filterOptions = [];

    protected function formatMessage(string $message, array $arguments = []): string
    {
        return MessageFormatter::formatMessage($message, $arguments);
    }

    /**
     * Renders the header cell.
     */
    public function renderHeaderCell(): string
    {
        return Html::tag('th', $this->renderHeaderCellContent(), $this->headerOptions);
    }

    /**
     * Renders the footer cell.
     */
    public function renderFooterCell(): string
    {
        return Html::tag('td', $this->renderFooterCellContent(), $this->footerOptions);
    }

    /**
     * Renders a data cell.
     *
     * @param array $model the data model being rendered
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data item among the item array returned by
     *     [[GridView::dataProvider]].
     * @return string the rendering result
     */
    public function renderDataCell(array $model, $key, int $index): string
    {
        if ($this->contentOptions instanceof Closure) {
            $options = call_user_func($this->contentOptions, $model, $key, $index, $this);
        } else {
            $options = $this->contentOptions;
        }

        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
    }

    /**
     * Renders the filter cell.
     */
    public function renderFilterCell(): string
    {
        return Html::tag('td', $this->renderFilterCellContent(), $this->filterOptions);
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
        return trim($this->header) !== '' ? $this->header : $this->getHeaderCellLabel();
    }

    /**
     * Returns header cell label.
     * This method may be overridden to customize the label of the header cell.
     *
     * @return string label
     */
    protected function getHeaderCellLabel(): string
    {
        return $this->grid->getEmptyCell();
    }

    /**
     * Renders the footer cell content.
     * The default implementation simply renders [[footer]].
     * This method may be overridden to customize the rendering of the footer cell.
     *
     * @return string the rendering result
     */
    protected function renderFooterCellContent(): string
    {
        return trim($this->footer) !== '' ? $this->footer : $this->grid->getEmptyCell();
    }

    /**
     * Renders the data cell content.
     *
     * @param array $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the models array returned by
     *     [[GridView::dataProvider]].
     * @return string the rendering result
     */
    protected function renderDataCellContent(array $model, $key, int $index): string
    {
        if ($this->content !== null) {
            return call_user_func($this->content, $model, $key, $index, $this);
        }

        return $this->grid->getEmptyCell();
    }

    /**
     * Renders the filter cell content.
     * The default implementation simply renders a space.
     * This method may be overridden to customize the rendering of the filter cell (if any).
     *
     * @return string the rendering result
     */
    protected function renderFilterCellContent(): string
    {
        return $this->grid->getEmptyCell();
    }

    public function grid(GridView $view): self
    {
        $this->grid = $view;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
