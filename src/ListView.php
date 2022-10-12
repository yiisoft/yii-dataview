<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Closure;
use InvalidArgumentException;
use Throwable;
use Yiisoft\Html\Tag\Div;
use Yiisoft\View\Exception\ViewNotFoundException;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Exception\WebViewNotSetException;

/**
 * The ListView widget is used to display data from data provider. Each data model is rendered using the view specified.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ListView extends BaseListView
{
    private Closure $afterItem;
    private Closure $beforeItem;
    /** @var callable|string|null */
    private $itemView = null;
    private array $itemViewAttributes = [];
    private string $separator = "\n";
    private array $viewParams = [];
    private WebView|null $webView = null;

    /**
     * Return new instance with afterItem closure.
     *
     * @param Closure $value an anonymous function that is called once after rendering each data.
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

    public function getWebView(): WebView
    {
        if ($this->webView === null) {
            throw new WebViewNotSetException();
        }

        return $this->webView;
    }

    /**
     * Return new instance with itemView closure.
     *
     * @param Closure|string $value the name of the view for rendering each data item, or a callback (e.g. an anonymous
     * function) for rendering each data item. If it specifies a view name, the following variables will be available in
     * the view:
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
     * return new instance with the HTML attributes for the container of item view.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function itemViewAttributes(array $values): self
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
     * Return new instance with the WebView object.
     *
     * @param WebView $value the WebView object.
     */
    public function webView(WebView $value): self
    {
        $new = clone $this;
        $new->webView = $value;

        return $new;
    }

    /**
     * Renders a single data model.
     *
     * @param array|object $data The data to be rendered.
     * @param mixed $key The key value associated with the data.
     * @param int $index The zero-based index of the data array.
     *
     * @throws Throwable|ViewNotFoundException
     */
    protected function renderItem(array|object $data, mixed $key, int $index): string
    {
        $content = '';

        if ($this->itemView === null) {
            throw new InvalidArgumentException('The "itemView" property must be set.');
        }

        if (is_string($this->itemView)) {
            $content = $this->getWebView()->render(
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
            $content = (string) call_user_func($this->itemView, $data, $key, $index, $this);
        }

        return Div::tag()
            ->addAttributes($this->itemViewAttributes)
            ->content(PHP_EOL . $content)
            ->encode(false)
            ->render();
    }

    /**
     * Renders all data models.
     *
     * @throws Throwable|ViewNotFoundException
     */
    protected function renderItems(): string
    {
        $data = $this->getDataReader();
        $keys = array_keys($data);
        $rows = [];

        /** @psalm-var array<array-key,array|object> $data */
        foreach (array_values($data) as $index => $value) {
            $key = $keys[$index];

            if ('' !== ($before = $this->renderBeforeItem($value, $key, $index))) {
                $rows[] = $before;
            }

            $rows[] = $this->renderItem($value, $key, $index);

            if ('' !== ($after = $this->renderAfterItem($value, $key, $index))) {
                $rows[] = $after;
            }
        }

        return implode($this->separator, $rows);
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
     * @return string {@see afterItem} call result when {@see afterItem} is not a closure.
     *
     * {@see afterItem}
     */
    private function renderAfterItem(array|object $data, mixed $key, int $index): string
    {
        $result = '';

        if (!empty($this->afterItem)) {
            $result = (string) call_user_func($this->afterItem, $data, $key, $index, $this);
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
     * @return string {@see beforeItem} call result or `null` when {@see beforeItem} is not a closure.
     *
     * {@see beforeItem}
     */
    private function renderBeforeItem(array|object $data, mixed $key, int $index): string
    {
        $result = '';

        if (!empty($this->beforeItem)) {
            $result = (string) call_user_func($this->beforeItem, $data, $key, $index, $this);
        }

        return $result;
    }
}
