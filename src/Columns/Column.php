<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Columns;

use Closure;
use JsonException;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\GridView;

use function call_user_func;
use function trim;

/**
 * Column is the base class of all {@see GridView} column classes.
 *
 * For more details and usage information on Column, see the [guide article on data widgets](guide:output-data-widgets).
 */
abstract class Column
{
    /** @var callable|Closure */
    protected $content;
    protected array $contentOptions = [];
    protected GridView $grid;
    protected array $filterOptions = [];
    protected array $footerOptions = [];
    protected string $header = '';
    protected array $headerOptions = [];
    protected bool $visible = true;
    protected array $options = [];
    private string $footer = '';

    public function renderHeaderCell(): string
    {
        return Html::tag('th', $this->renderHeaderCellContent(), $this->headerOptions)->encode(false)->render();
    }

    public function renderFooterCell(): string
    {
        return Html::tag('td', $this->renderFooterCellContent(), $this->footerOptions)->encode(false)->render();
    }

    /**
     * Renders a data cell.
     *
     * @param array|object $model the data model being rendered
     * @param mixed $key the key associated with the data model
     * @param mixed $index the zero-based index of the data item among the item array returned by
     * {GridView::dataReader}.
     *
     * @throws JsonException
     *
     * @return string the rendering result
     */
    public function renderDataCell($model, $key, $index): string
    {
        if ($this->contentOptions instanceof Closure) {
            $options = call_user_func($this->contentOptions, $model, $key, $index, $this);
        } else {
            $options = $this->contentOptions;
        }

        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options)->encode(false)->render();
    }

    /**
     * Renders the filter cell.
     */
    public function renderFilterCell(): string
    {
        return Html::tag('td', $this->renderFilterCellContent(), $this->filterOptions)->encode(false)->render();
    }

    /**
     * @param callable|null $content This is a callable that will be used to generate the content of each cell.
     * The signature of the function should be the following: `function ($model, $key, $index, $column)`.
     * Where `$model`, `$key`, and `$index` refer to the model, key and index of the row currently being rendered and
     * `$column` is a reference to the {@see Column} object.
     *
     * @return $this
     */
    public function content(?callable $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param string $footer the footer cell content. Note that it will not be HTML-encoded.
     *
     * @return $this
     */
    public function footer(string $footer): self
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * @param GridView $grid the grid view object that owns this column.
     *
     * @return $this
     */
    public function grid(GridView $grid): self
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * @return bool whether this column is visible. Defaults to true.
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string $header the header cell content. Note that it will not be HTML-encoded.
     *
     * @return $this
     */
    public function header(string $header): self
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @param array $headerOptions the HTML attributes for the header cell tag.
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     *
     * @return $this
     */
    public function headerOptions(array $headerOptions): self
    {
        $this->headerOptions = $headerOptions;

        return $this;
    }

    /**
     * @param array $contentOptions the HTML attributes for the data cell tag. This can either be an array of attributes
     * or an anonymous function ({@see Closure}) that returns such an array. The signature of the function should be the
     * following: `function ($model, $key, $index, $column)`. Where `$model`, `$key`, and `$index` refer to the model,
     * key and index of the row currently being rendered and `$column` is a reference to the {@see Column} object.
     * A function may be used to assign different attributes to different rows based on the data in that row.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function contentOptions(array $contentOptions): self
    {
        $this->contentOptions = $contentOptions;

        return $this;
    }

    /**
     * @param array $options the HTML attributes for the column group tag.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function options(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param array $footerOptions the HTML attributes for the footer cell tag.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function footerOptions(array $footerOptions): self
    {
        $this->footerOptions = $footerOptions;

        return $this;
    }

    /**
     * @param array $filterOptions the HTML attributes for the filter cell tag.
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function filterOptions(array $filterOptions): self
    {
        $this->filterOptions = $filterOptions;

        return $this;
    }

    /**
     * Returns header cell label.
     *
     * This method may be overridden to customize the label of the header cell.
     *
     * @return string label
     */
    protected function getHeaderCellLabel(): string
    {
        return $this->grid->getEmptyCell();
    }

    /**
     * Renders the data cell content.
     *
     * @param array $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the models array returned by
     * {@see GridView::dataReader}.
     *
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
     *
     * The default implementation simply renders a space.
     * This method may be overridden to customize the rendering of the filter cell (if any).
     *
     * @return string the rendering result
     */
    protected function renderFilterCellContent(): string
    {
        return $this->grid->getEmptyCell();
    }

    /**
     * Renders the footer cell content.
     *
     * The default implementation simply renders {@see footer}.
     * This method may be overridden to customize the rendering of the footer cell.
     *
     * @return string the rendering result
     */
    protected function renderFooterCellContent(): string
    {
        return trim($this->footer) !== '' ? $this->footer : $this->grid->getEmptyCell();
    }

    /**
     * Renders the header cell content.
     *
     * The default implementation simply renders {@see header}.
     * This method may be overridden to customize the rendering of the header cell.
     *
     * @return string the rendering result
     */
    protected function renderHeaderCellContent(): string
    {
        return trim($this->header) !== '' ? $this->header : $this->getHeaderCellLabel();
    }
}
