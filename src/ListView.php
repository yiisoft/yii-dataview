<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Closure;
use InvalidArgumentException;
use Yiisoft\Html\Html;
use Yiisoft\View\Exception\ViewNotFoundException;
use Yiisoft\View\View;

use function is_string;

/**
 * The ListView widget displays data from data provider. Each data model is rendered using the view specified.
 */
final class ListView extends BaseListView
{
    private ?Closure $afterItem = null;
    private ?Closure $beforeItem = null;

    /**
     * @psalm-var non-empty-string|null
     */
    private ?string $itemsWrapperTag = 'ul';
    private array $itemsWrapperAttributes = [];

    /**
     * @var callable|string|null
     */
    private $itemView = null;

    /**
     * @psalm-var non-empty-string|null
     */
    private ?string $itemViewTag = 'li';
    private array|Closure $itemViewAttributes = [];
    private string $separator = "\n";
    private array $viewParams = [];
    private ?View $view = null;

    /**
     * Return new instance with afterItem closure.
     *
     * @param Closure $value An anonymous function that is called once after rendering each data.
     *
     * It should have the same signature as {@see beforeItem}.
     *
     * The return result of the function will be rendered directly.
     *
     * Note: If the function returns `null`, nothing will be rendered after the item.
     *
     * {@see renderAfterItem}
     */
    public function afterItem(Closure $value): self
    {
        $new = clone $this;
        $new->afterItem = $value;

        return $new;
    }

    /**
     * Return new instance with beforeItem closure.
     *
     * @param Closure $value an anonymous function that is called once before rendering each data.
     *
     * It should have the following signature:
     *
     * ```php
     * function ($data, $key, $index, $widget)
     * ```
     *
     * - `$data`: The current data being rendered.
     * - `$key`: The key value associated with the current data.
     * - `$index`: The zero-based index of the data in the array.
     * - `$widget`: The list view object.
     *
     * The return result of the function will be rendered directly.
     *
     * Note: If the function returns `null`, nothing will be rendered before the item.
     *
     * {@see renderBeforeItem}
     */
    public function beforeItem(Closure $value): self
    {
        $new = clone $this;
        $new->beforeItem = $value;

        return $new;
    }

    /**
     * Set the HTML tag for the items wrapper.
     *
     * @param string|null $tag
     * @return $this
     */
    public function itemsWrapperTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('The "itemsWrapperTag" property cannot be empty.');
        }

        $new = clone $this;
        $new->itemsWrapperTag = $tag;
        return $new;
    }

    /**
     * Set the HTML attributes for the container of the items wrapper.
     *
     * @param array $values
     * @return $this
     */
    public function itemsWrapperAttributes(array $values): self
    {
        $new = clone $this;
        $new->itemsWrapperAttributes = $values;
        return $new;
    }

    /**
     * Return new instance with itemView closure.
     *
     * @param Closure|string $value the full path of the view for rendering each data item, or a callback (e.g.
     * an anonymous function) for rendering each data item. If it specifies a view name, the following variables will be
     * available in the view:
     *
     * - `$data`: The data model.
     * - `$key`: The key value associated with the data item.
     * - `$index`: The zero-based index of the data item in the items array.
     * - `$widget`: The list view widget instance.
     *
     * Note that the view name is resolved into the view file by the current context of the {@see view} object.
     *
     * If this property is specified as a callback, it should have the following signature:
     *
     * ```php
     * function ($data, $key, $index, $widget)
     * ```
     */
    public function itemView(string|Closure $value): self
    {
        $new = clone $this;
        $new->itemView = $value;
        return $new;
    }

    /**
     * Set the HTML tag for the container of item view.
     *
     * @param string|null $tag
     * @return $this
     */
    public function itemViewTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('The "itemViewTag" property cannot be empty.');
        }

        $new = clone $this;
        $new->itemViewTag = $tag;
        return $new;
    }

    /**
     * Return new instance with the HTML attributes for the container of item view.
     *
     * @param array|Closure $values Attribute values indexed by attribute names.
     * If this property is specified as a function, it must return an array of attributes and have the following
     * signature:
     *
     * ```php
     * function ($data, $key, $index, $widget)
     * ```
     * Also, each attribute value can be a function too, with the same signature as in:
     *
     * ```php
     * [
     *     'class' => static fn($data, $key, $index, $widget) => "custom-class-{$data['id']}",
     * ]
     * ```
     * @return ListView
     */
    public function itemViewAttributes(array|Closure $values): self
    {
        $new = clone $this;
        $new->itemViewAttributes = $values;

        return $new;
    }

    /**
     * Return new instance with the separator between the items.
     *
     * @param string $separator the HTML code to be displayed between any two consecutive items.
     */
    public function separator(string $separator): self
    {
        $new = clone $this;
        $new->separator = $separator;

        return $new;
    }

    /**
     * Return new instance with the parameters for the view.
     *
     * @param array $viewParams additional parameters to be passed to {@see itemView} when it is being rendered.
     *
     * This property is used only when {@see itemView} is a string representing a view name.
     */
    public function viewParams(array $viewParams): self
    {
        $new = clone $this;
        $new->viewParams = $viewParams;

        return $new;
    }

    /**
     * Renders a single data model.
     *
     * @param array|object $data The data to be rendered.
     * @param mixed $key The key value associated with the data.
     * @param int $index The zero-based index of the data array.
     *
     * @throws ViewNotFoundException If the item view file doesn't exist.
     */
    protected function renderItem(array|object $data, mixed $key, int $index): string
    {
        $content = '';

        if ($this->itemView === null) {
            throw new InvalidArgumentException('The "itemView" property must be set.');
        }

        if (is_string($this->itemView)) {
            $content = $this->getView()->render(
                $this->itemView,
                array_merge(
                    [
                        'data' => $data,
                        'index' => $index,
                        'key' => $key,
                        'widget' => $this,
                    ],
                    $this->viewParams
                )
            );
        }

        if ($this->itemView instanceof Closure) {
            $content = (string)call_user_func($this->itemView, $data, $key, $index, $this);
        }

        $itemViewAttributes = is_callable($this->itemViewAttributes)
            ? (array)call_user_func($this->itemViewAttributes, $data, $key, $index, $this)
            : $this->itemViewAttributes;

        foreach ($itemViewAttributes as $i => $attribute) {
            if (is_callable($attribute)) {
                $itemViewAttributes[$i] = $attribute($data, $key, $index, $this);
            }
        }

        return $this->itemViewTag === null
            ? $content
            : Html::tag($this->itemViewTag)
                ->attributes($itemViewAttributes)
                ->content("\n" . $content)
                ->encode(false)
                ->render();
    }

    /**
     * Renders all data models.
     *
     * @throws ViewNotFoundException If the item view file doesn't exist.
     */
    protected function renderItems(array $items, \Yiisoft\Validator\Result $filterValidationResult): string
    {
        $keys = array_keys($items);
        $rows = [];

        /** @psalm-var array<array-key,array|object> $items */
        foreach (array_values($items) as $index => $value) {
            $key = $keys[$index];

            if ('' !== ($before = $this->renderBeforeItem($value, $key, $index))) {
                $rows[] = $before;
            }

            $rows[] = $this->renderItem($value, $key, $index);

            if ('' !== ($after = $this->renderAfterItem($value, $key, $index))) {
                $rows[] = $after;
            }
        }

        $content = implode($this->separator, $rows);

        return $this->itemsWrapperTag === null
            ? $content
            : Html::tag($this->itemsWrapperTag, "\n" . $content . "\n", $this->itemsWrapperAttributes)
                ->encode(false)
                ->render();
    }

    /**
     * Calls {@see afterItem} closure, returns execution result.
     *
     * If {@see afterItem} is not a closure, `null` will be returned.
     *
     * @param array|object $data The data to be rendered.
     * @param mixed $key The key value associated with the data.
     * @param int $index The zero-based index of the data.
     *
     * @return string call result when {@see afterItem} is not a closure.
     *
     * {@see afterItem}
     */
    private function renderAfterItem(array|object $data, mixed $key, int $index): string
    {
        $result = '';

        if (!empty($this->afterItem)) {
            $result = (string)call_user_func($this->afterItem, $data, $key, $index, $this);
        }

        return $result;
    }

    /**
     * Calls {@see beforeItem} closure, returns execution result.
     *
     * If {@see beforeItem} is not a closure, `null` will be returned.
     *
     * @param array|object $data The data to be rendered.
     * @param mixed $key The key value associated with the data.
     * @param int $index The zero-based index of the data.
     *
     * @return string call result or `null` when {@see beforeItem} is not a closure.
     *
     * {@see beforeItem}
     */
    private function renderBeforeItem(array|object $data, mixed $key, int $index): string
    {
        $result = '';

        if (!empty($this->beforeItem)) {
            $result = (string)call_user_func($this->beforeItem, $data, $key, $index, $this);
        }

        return $result;
    }

    private function getView(): View
    {
        if ($this->view === null) {
            $this->view = new View();
        }
        return $this->view;
    }
}
