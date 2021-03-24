<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Columns;

use Closure;
use InvalidArgumentException;
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
    /** @var callable */
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
     * @param array|object $model the data model being rendered.
     * @param mixed $key the key associated with the data model.
     * @param int $index the zero-based index of the data item among the item array returned by
     * {GridView::dataReader}.
     *
     * @throws JsonException
     *
     * @return string the rendering result.
     */
    public function renderDataCell($model, $key, int $index): string
    {
        return Html::tag(
            'td',
            $this->renderDataCellContent($model, $key, $index),
            $this->contentOptions
        )->encode(false)->render();
    }

    /**
     * Renders the filter cell.
     */
    public function renderFilterCell(): string
    {
        return Html::tag('td', $this->renderFilterCellContent(), $this->filterOptions)->encode(false)->render();
    }

    /**
     * @param callable $content This is a callable that will be used to generate the content of each cell.
     * The signature of the function should be the following: `function ($model, $key, $index, $column)`.
     * Where `$model`, `$key`, and `$index` refer to the model, key and index of the row currently being rendered and
     * `$column` is a reference to the {@see Column} object.
     *
     * @return $this
     */
    public function content(callable $content): self
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
     * @param bool whether this column is visible. Defaults to true.
     *
     * @return $this
     */
    public function invisible(): self
    {
        $this->visible = false;

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
     * @param array|object $model the data model.
     * @param mixed $key the key associated with the data model.
     * @param int $index the zero-based index of the data model among the models array returned by
     * {@see GridView::dataReader}.
     *
     * @return string the rendering result.
     */
    protected function renderDataCellContent($model, $key, int $index): string
    {
        $html = $this->grid->getEmptyCell();

        if (!empty($this->content)) {
            $html = (string) call_user_func($this->content, $model, $key, $index, $this);
        }

        return $html;
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

    /**
     * Generates an appropriate input name for the specified attribute name or expression.
     *
     * This method generates a name that can be used as the input name to collect user input for the specified
     * attribute. The name is generated according to the of the form and the given attribute name. For example, if the
     * form name of the `Post` form is `Post`, then the input name generated for the `content` attribute would be
     * `Post[content]`.
     *
     * @param string $formName the form name.
     * @param string $attribute the attribute name or expression.
     *
     * @throws InvalidArgumentException if the attribute name contains non-word characters
     * or empty form name for tabular inputs
     *
     * @return string the generated input name.
     */
    protected function getInputName(string $formName, string $attribute): string
    {
        $data = $this->parseAttribute($attribute);

        if ($formName === '' && $data['prefix'] === '') {
            return $attribute;
        }

        if ($formName !== '') {
            return $formName . $data['prefix'] . '[' . $data['name'] . ']' . $data['suffix'];
        }

        throw new InvalidArgumentException($formName . '::formName() cannot be empty for tabular inputs.');
    }

    /**
     * This method parses an attribute expression and returns an associative array containing real attribute name,
     * prefix and suffix.
     *
     * For example: `['name' => 'content', 'prefix' => '', 'suffix' => '[0]']`
     *
     * An attribute expression is an attribute name prefixed and/or suffixed with array indexes. It is mainly used in
     * tabular data input and/or input of array type. Below are some examples:
     *
     * - `[0]content` is used in tabular data input to represent the "content" attribute for the first model in tabular
     *    input;
     * - `dates[0]` represents the first array element of the "dates" attribute;
     * - `[0]dates[0]` represents the first array element of the "dates" attribute for the first model in tabular
     *    input.
     *
     * @param string $attribute the attribute name or expression
     *
     * @throws InvalidArgumentException if the attribute name contains non-word characters.
     *
     * @return array
     *
     * @psalm-return array<array-key,string>
     */
    private function parseAttribute(string $attribute): array
    {
        if (!preg_match('/(^|.*\])([\w\.\+]+)(\[.*|$)/u', $attribute, $matches)) {
            throw new InvalidArgumentException('Attribute name must contain word characters only.');
        }

        return [
            'name' => $matches[2],
            'prefix' => $matches[1],
            'suffix' => $matches[3],
        ];
    }
}
