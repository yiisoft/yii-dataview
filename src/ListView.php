<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Closure;
use InvalidArgumentException;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Html\Html;
use Yiisoft\Validator\Result as ValidationResult;
use Yiisoft\View\Exception\ViewNotFoundException;
use Yiisoft\View\View;

/**
 * ListView is a flexible widget for displaying a list of data items with customizable rendering and layout.
 */
final class ListView extends BaseListView
{
    private ?Closure $afterItemCallback = null;
    private ?Closure $beforeItemCallback = null;

    /**
     * @psalm-var non-empty-string|null
     */
    private ?string $itemListTag = 'ul';
    private array $itemListAttributes = [];
    private ?string $itemView = null;
    private ?Closure $itemCallback = null;

    /**
     * @psalm-var non-empty-string|null
     */
    private ?string $itemTag = 'li';
    private array|Closure $itemAttributes = [];
    private string $separator = "\n";
    private array $itemViewParameters = [];
    private ?View $view = null;

    /**
     * Return new instance with a function that is called after rendering each item..
     *
     * @param Closure $callback A callback with the following signature:
     *
     * ```php
     * function (ListItemContext $context): ?string
     * ```
     *
     * The return result of the function will be rendered directly.
     * If the result is `null`, nothing is rendered.
     *
     * {@see renderAfterItem}
     */
    public function afterItem(Closure $callback): self
    {
        $new = clone $this;
        $new->afterItemCallback = $callback;

        return $new;
    }

    /**
     * Return new instance with a function that is called before rendering each item.
     *
     * @param Closure $callback A callback with the following signature:
     *
     *  ```php
     *  function (ListItemContext $context): ?string
     *  ```
     *
     *  The return result of the function will be rendered directly.
     *  If the result is `null`, nothing is rendered.
     *
     * {@see renderBeforeItem}
     */
    public function beforeItem(Closure $callback): self
    {
        $new = clone $this;
        $new->beforeItemCallback = $callback;

        return $new;
    }

    /**
     * Set the HTML tag for the item list.
     *
     * @param string|null $tag
     * @return $this
     */
    public function itemListTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('The "itemListTag" cannot be empty.');
        }

        $new = clone $this;
        $new->itemListTag = $tag;
        return $new;
    }

    /**
     * Set the HTML attributes for the item list.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     * @return $this
     */
    public function itemListAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->itemListAttributes = $attributes;
        return $new;
    }

    /**
     * Return new instance with item view defined.
     *
     * @param string $view A full path of the view to use for each list item.
     *
     * The following variables are available within the view:
     *
     * - `$data`: A data item.
     * - `$key`: Key associated with the item.
     * - `$index`: Zero-based index of the item.
     * - `$widget`: List view widget instance.
     *
     * The view name is resolved into the view file by the current context of the {@see view} object.
     */
    public function itemView(string $view): self
    {
        $new = clone $this;
        $new->itemView = $view;
        return $new;
    }

    /**
     * Return new instance with item callback defined.
     *
     * @param Closure $callback A callback for to use for each list item rendering.
     *
     * The callback signature should be the following:
     *
     *  ```php
     *  function ($data, $key, int $index, ListView $widget): string
     *  ```
     *
     *  The returned string is the rendered item markup.
     */
    public function itemCallback(Closure $callback): self
    {
        $new = clone $this;
        $new->itemCallback = $callback;
        return $new;
    }

    /**
     * Set the HTML tag for the list item.
     *
     * @param string|null $tag
     * @return $this
     */
    public function itemTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('The "itemTag" cannot be empty.');
        }

        $new = clone $this;
        $new->itemTag = $tag;
        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for list item.
     *
     * @param array|Closure $attributes Attribute values/callbacks indexed by attribute names or a callback
     * with the following signature:
     *
     * ```php
     * function (ListItemContext $context): array
     * ```
     *
     * Returned is an array of attributes.
     *
     * In the array of attributes, each attribute value can be a callback like the following:
     *
     * ```php
     * [
     *     'class' => static fn(ListItemContext $context) => "custom-class-{$context->data['id']}",
     * ]
     * ```
     */
    public function itemAttributes(array|Closure $attributes): self
    {
        $new = clone $this;
        $new->itemAttributes = $attributes;

        return $new;
    }

    /**
     * Return new instance with the separator between the items.
     *
     * @param string $separator The HTML code to be displayed between any two consecutive items.
     */
    public function separator(string $separator): self
    {
        $new = clone $this;
        $new->separator = $separator;

        return $new;
    }

    /**
     * Return new instance with the additional parameters for the view.
     *
     * @param array $parameters Additional parameters to be passed to the view set in {@see itemView()} when it is
     * being rendered.
     */
    public function itemViewParameters(array $parameters): self
    {
        $new = clone $this;
        $new->itemViewParameters = $parameters;

        return $new;
    }

    /**
     * Renders a single list item.
     *
     * @param ListItemContext $context The context to take into account when rendering.
     *
     * @throws ViewNotFoundException If the item view file doesn't exist.
     * @throws InvalidArgumentException If both itemView and itemCallback aren't set.
     * @return string Rendered HTML.
     */
    protected function renderItem(ListItemContext $context): string
    {
        $content = '';

        if ($this->itemView === null && $this->itemCallback === null) {
            throw new InvalidArgumentException('Either "itemView" or "itemCallback" must be set.');
        }

        if ($this->itemView !== null) {
            $content = $this->getView()->render(
                $this->itemView,
                array_merge(
                    [
                        'data' => $context->data,
                        'index' => $context->index,
                        'key' => $context->key,
                        'widget' => $this,
                    ],
                    $this->itemViewParameters
                )
            );
        }

        if ($this->itemCallback !== null) {
            $content = (string)($this->itemCallback)($context);
        }

        $itemAttributes = is_callable($this->itemAttributes)
            ? (array)($this->itemAttributes)($context)
            : $this->itemAttributes;

        foreach ($itemAttributes as $i => $attribute) {
            if (is_callable($attribute)) {
                $itemAttributes[$i] = $attribute($context);
            }
        }

        return $this->itemTag === null
            ? $content
            : Html::tag($this->itemTag)
                ->attributes($itemAttributes)
                ->content("\n" . $content)
                ->encode(false)
                ->render();
    }

    /**
     * Renders all data models.
     *
     * @throws ViewNotFoundException If the item view file doesn't exist.
     */
    protected function renderItems(
        array $items,
        ValidationResult $filterValidationResult,
        ?ReadableDataInterface $preparedDataReader,
    ): string {
        $keys = array_keys($items);
        $rows = [];

        /** @psalm-var array<array-key,array|object> $items */
        foreach (array_values($items) as $index => $value) {
            $key = $keys[$index];

            $itemContext = new ListItemContext($value, $key, $index, $this);

            if ('' !== ($before = $this->renderBeforeItem($itemContext))) {
                $rows[] = $before;
            }

            $rows[] = $this->renderItem($itemContext);

            if ('' !== ($after = $this->renderAfterItem($itemContext))) {
                $rows[] = $after;
            }
        }

        $content = implode($this->separator, $rows);

        return $this->itemListTag === null
            ? $content
            : Html::tag($this->itemListTag, "\n" . $content . "\n", $this->itemListAttributes)
                ->encode(false)
                ->render();
    }

    /**
     * Calls {@see afterItem()} callback and returns execution result or an empty string
     * if callback is not defined.
     *
     * @param ListItemContext $context Context of the item to be rendered.
     *
     * @return string Call result.
     */
    private function renderAfterItem(ListItemContext $context): string
    {
        $result = '';

        if (!empty($this->afterItemCallback)) {
            $result = (string)($this->afterItemCallback)($context);
        }

        return $result;
    }

    /**
     * Calls {@see beforeItem} callback and returns execution result or an empty string
     *  if callback is not defined.
     *
     * @param ListItemContext $context Context of the item to be rendered.
     *
     * @return string Call result.
     */
    private function renderBeforeItem(ListItemContext $context): string
    {
        $result = '';

        if (!empty($this->beforeItemCallback)) {
            $result = (string)($this->beforeItemCallback)($context);
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
