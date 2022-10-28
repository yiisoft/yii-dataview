<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;
use Yiisoft\Html\Tag\Td;
use Yiisoft\Html\Tag\Th;

use function mb_strtolower;

/**
 * Column is the base class of all {@see GridView} column classes.
 */
abstract class AbstractColumn
{
    private array $attributes = [];
    private Closure|null $content = null;
    private array $contentAttributes = [];
    private string $emptyCell = '';
    private array $filterAttributes = [];
    private string $footer = '';
    private array $footerAttributes = [];
    private string $label = '';
    private array $labelAttributes = [];
    protected bool $visible = true;

    final public function __construct()
    {
    }

    /**
     * Return new instance with the HTML attributes of column.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function attributes(array $values): static
    {
        $new = clone $this;
        $new->attributes = $values;

        return $new;
    }

    /**
     * Return new instance with the column content.
     *
     * @param Closure $value This is a callable that will be used to generate the content.
     *
     * The signature of the function should be the following: `function ($data, $key, $index, $column)`.
     *
     * Where `$data`, `$key`, and `$index` refer to the data, key and index of the row currently being rendered
     * and `$column` is a reference to the {@see Column} object.
     */
    public function content(Closure $value): static
    {
        $new = clone $this;
        $new->content = $value;

        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the column content.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function contentAttributes(array $values): static
    {
        $new = clone $this;
        $new->contentAttributes = $values;

        return $new;
    }

    /**
     * Return new instance with the data label for the column content.
     *
     * @param string $value The data label for the column content.
     */
    public function dataLabel(string $value): static
    {
        $new = clone $this;
        $new->contentAttributes['data-label'] = $value;

        return $new;
    }

    /**
     * Return new instance with the HTML display when the content is empty.
     *
     * @param string $value The HTML display when the content of a cell is empty. This property is used to render cells
     * that have no defined content, e.g. empty footer or filter cells.
     */
    public function emptyCell(string $value): static
    {
        $new = clone $this;
        $new->emptyCell = $value;

        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the filter cell.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function filterAttributes(array $values): static
    {
        $new = clone $this;
        $new->filterAttributes = $values;

        return $new;
    }

    /**
     * Return new instance with the footer content.
     *
     * @param string $value The footer content.
     */
    public function footer(string $value): static
    {
        $new = clone $this;
        $new->footer = $value;

        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the footer cell.
     *
     * @param array $value Attribute values indexed by attribute names.
     */
    public function footerAttributes(array $value): static
    {
        $new = clone $this;
        $new->footerAttributes = $value;

        return $new;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Return new instance with the label for the column.
     *
     * @param string $value The label to be displayed in the {@see header|header cell} and also to be used as the
     * sorting link label when sorting is enabled for this column.
     *
     * If it is not set and the active record classes provided by the GridViews data provider are instances of the
     * object data, the label will be determined using Otherwise {@see Inflector::toHumanReadable()} will be used to
     * get a label.
     */
    public function label(string $value): static
    {
        $new = clone $this;
        $new->label = $value;

        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the header cell.
     *
     * @param array $value Attribute values indexed by attribute names.
     */
    public function labelAttributes(array $value): static
    {
        $new = clone $this;
        $new->labelAttributes = $value;

        return $new;
    }

    /**
     * Return new instance with the name of the column.
     *
     * @param string $value The name of the column.
     */
    public function name(string $value): static
    {
        $new = clone $this;
        $new->contentAttributes['name'] = $value;

        return $new;
    }

    /**
     * Renders a data cell.
     *
     * @param array|object $data The data.
     * @param mixed $key The key associated with the data.
     * @param int $index The zero-based index of the data in the data provider.
     */
    public function renderDataCell(array|object $data, mixed $key, int $index): string
    {
        $contentAttributes = $this->contentAttributes;

        /**
         * @var string $name
         * @var mixed $value
         */
        foreach ($contentAttributes as $name => $value) {
            if ($value instanceof Closure) {
                /** @var mixed */
                $contentAttributes[$name] = $value($data, $key, $index, $this);
            }
        }

        if (!array_key_exists('data-label', $contentAttributes) && $this->getLabel() !== '') {
            $contentAttributes['data-label'] = mb_strtolower($this->getLabel(), 'UTF-8');
        }

        return Td::tag()
            ->addAttributes($contentAttributes)
            ->content($this->renderDataCellContent($data, $key, $index))
            ->encode(false)
            ->render();
    }

    /**
     * Renders the filter cell.
     */
    public function renderFilterCell(): string
    {
        return Th::tag()
            ->addAttributes($this->filterAttributes)
            ->content($this->renderFilterCellContent())
            ->encode(false)
            ->render();
    }

    /**
     * Renders the footer cell.
     */
    public function renderFooterCell(): string
    {
        return Td::tag()
            ->addAttributes($this->footerAttributes)
            ->content($this->renderFooterCellContent())
            ->encode(false)
            ->render();
    }

    /**
     * Renders the header cell.
     */
    public function renderHeaderCell(): string
    {
        return Th::tag()
            ->addAttributes($this->labelAttributes)
            ->content($this->renderHeaderCellContent())
            ->encode(false)
            ->render();
    }

    /**
     * Return new instance specifying whether the column is visible or not.
     *
     * @param bool $value Whether the column is visible or not.
     */
    public function visible(bool $value): static
    {
        $new = clone $this;
        $new->visible = $value;

        return $new;
    }

    public static function create(): static
    {
        return new static();
    }

    protected function getContent(): Closure|null
    {
        return $this->content;
    }

    protected function getContentAttributes(): array
    {
        return $this->contentAttributes;
    }

    protected function getEmptyCell(): string
    {
        return $this->emptyCell;
    }

    protected function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Renders the data cell content.
     *
     * @param array|object $data The data.
     * @param mixed $key The key associated with the data.
     * @param int $index The zero-based index of the data in the data provider.
     */
    protected function renderDataCellContent(array|object $data, mixed $key, int $index): string
    {
        $html = $this->emptyCell;

        if ($this->content !== null) {
            $html = (string) call_user_func($this->content, $data, $key, $index, $this);
        }

        return $html;
    }

    /**
     * Renders the filter cell content.
     *
     * The default implementation simply renders a space.
     * This method may be overridden to customize the rendering of the filter cell (if any).
     */
    protected function renderFilterCellContent(): string
    {
        return $this->emptyCell;
    }

    /**
     * Renders the header cell content.
     *
     * The default implementation simply renders {@see header}.
     * This method may be overridden to customize the rendering of the header cell.
     */
    protected function renderHeaderCellContent(): string
    {
        return $this->getLabel() !== '' ? $this->getLabel() : $this->emptyCell;
    }

    /**
     * Renders the footer cell content.
     *
     * The default implementation simply renders {@see footer}.
     * This method may be overridden to customize the rendering of the footer cell.
     */
    private function renderFooterCellContent(): string
    {
        return $this->footer !== '' ? $this->footer : $this->emptyCell;
    }
}
