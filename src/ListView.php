<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Closure;
use InvalidArgumentException;
use Throwable;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\ViewContextInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Exception\InvalidConfigException;

use function array_keys;
use function array_merge;
use function array_values;
use function call_user_func;
use function is_array;
use function is_callable;
use function is_string;

/**
 * The ListView widget is used to display data from data provider. Each data model is rendered using the view specified.
 *
 * For more details and usage information on ListView:
 * see the [guide article on data widgets](guide:output-data-widgets).
 */
final class ListView extends BaseListView implements ViewContextInterface
{
    protected array $options = ['class' => 'list-view'];
    /** @var array|Closure */
    private $itemOptions = [];
    /** @var callable|string|null */
    private $itemView;
    private string $viewPath = '';
    private array $viewParams = [];
    private string $separator = "\n";
    /** @var callable|null */
    private $beforeItem;
    /** @var callable|null */
    private $afterItem;
    private Aliases $aliases;
    private WebView $webView;

    public function __construct(Aliases $aliases, TranslatorInterface $translator, WebView $webView)
    {
        $this->aliases = $aliases;
        $this->webView = $webView;

        parent::__construct($translator);
    }

    /**
     * Renders all data models.
     *
     * @throws Throwable
     *
     * @return string the rendering result
     */
    public function renderItems(): string
    {
        $models = $this->getDataReader();
        $keys = array_keys($models);
        $rows = [];

        foreach (array_values($models) as $index => $model) {
            $key = $keys[$index];
            if (($before = $this->renderBeforeItem($model, $key, $index)) !== null) {
                $rows[] = $before;
            }

            $rows[] = $this->renderItem($model, $key, $index);

            if (($after = $this->renderAfterItem($model, $key, $index)) !== null) {
                $rows[] = $after;
            }
        }

        return implode($this->separator, $rows);
    }

    /**
     * Calls {@see beforeItem} closure, returns execution result.
     *
     * If {@see beforeItem} is not a closure, `null` will be returned.
     *
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key value associated with the data model
     * @param mixed $index the zero-based index of the data model in the model array returned by
     * {@see PaginatorInterface}.
     *
     * @return string|null {@see beforeItem} call result or `null` when {@see beforeItem} is not a closure.
     *
     * @see beforeItem
     */
    protected function renderBeforeItem($model, $key, $index): ?string
    {
        if ($this->beforeItem instanceof Closure) {
            return call_user_func($this->beforeItem, $model, $key, $index, $this);
        }

        return null;
    }

    /**
     * Calls {@see afterItem} closure, returns execution result.
     *
     * If {@see afterItem} is not a closure, `null` will be returned.
     *
     * @param mixed $model the data model to be rendered.
     * @param mixed $key the key value associated with the data model.
     * @param mixed $index the zero-based index of the data model in the model array returned by
     * {@see PaginatorInterface}.
     *
     * @return string|null {@see afterItem} call result or `null` when {@see afterItem} is not a closure
     *
     * @see afterItem
     */
    protected function renderAfterItem($model, $key, $index): ?string
    {
        if ($this->afterItem instanceof Closure) {
            return call_user_func($this->afterItem, $model, $key, $index, $this);
        }

        return null;
    }

    /**
     * Renders a single data model.
     *
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key value associated with the data model
     * @param mixed $index the zero-based index of the data model in the model array returned by
     * {@see PaginatorInterface}.
     *
     * @throws Throwable
     *
     * @return string the rendering result
     */
    public function renderItem($model, $key, $index): string
    {
        if ($this->itemView === null) {
            $content = (string) $key;
        } elseif (is_string($this->itemView)) {
            $content = $this->webView->render(
                $this->getItemViewPath(),
                array_merge(
                    [
                        'model' => $model,
                        'key' => $key,
                        'index' => $index,
                        'widget' => $this,
                    ],
                    $this->viewParams
                ),
                $this
            );
        } elseif (is_callable($this->itemView)) {
            $content = call_user_func($this->itemView, $model, $key, $index, $this);
        } else {
            throw new InvalidConfigException('Unknown type of $itemView');
        }
        if ($this->itemOptions instanceof Closure) {
            $options = call_user_func($this->itemOptions, $model, $key, $index, $this);
        } else {
            $options = $this->itemOptions;
        }

        $tag = ArrayHelper::remove($options, 'tag', 'div');
        $options['data-key'] = is_array($key) ? json_encode(
            $key,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ) : (string)$key;
        $options['encode'] = false;

        return Html::tag($tag, $content, $options);
    }

    /**
     * Returns by default the base path configured in the view.
     *
     * @return string
     */
    public function getViewPath(): string
    {
        $path = $this->webView->getBasePath();

        if ($this->viewPath !== '') {
            $path = $this->viewPath;
        }

        return $this->aliases->get($path);
    }

    public function getItemViewPath(): ?string
    {
        if (is_callable($this->itemView)) {
            return ($this->itemView)();
        }

        return $this->itemView;
    }

    /**
     * @param callable|null $afterItem Closure an anonymous function that is called once AFTER rendering each data
     * model.
     *
     * It should have the same signature as {@see beforeItem}.
     *
     * The return result of the function will be rendered directly.
     *
     * Note: If the function returns `null`, nothing will be rendered after the item.
     *
     * @return ListView
     *
     * @see renderAfterItem
     */
    public function withAfterItem(?callable $afterItem): self
    {
        $new = clone $this;
        $new->afterItem = $afterItem;

        return $new;
    }

    /**
     * @param callable|null ?Closure an anonymous function that is called once BEFORE rendering each data model.
     *
     * It should have the following signature:
     *
     * ```php
     * function ($model, $key, $index, $widget)
     * ```
     *
     * - `$model`: the current data model being rendered
     * - `$key`: the key value associated with the current data model
     * - `$index`: the zero-based index of the data model in the model array returned by {@see PaginatorInterface}
     * - `$widget`: the ListView object
     *
     * The return result of the function will be rendered directly.
     *
     * Note: If the function returns `null`, nothing will be rendered before the item.
     *
     * @return $this
     *
     * @see renderBeforeItem
     */
    public function withBeforeItem(?callable $beforeItem): self
    {
        $new = clone $this;
        $new->beforeItem = $beforeItem;

        return $new;
    }

    /**
     * @param array|Closure $itemOptions the HTML attributes for the container of the rendering result of each data
     * model.
     *
     * This can be either an array specifying the common HTML attributes for rendering each data item, or an anonymous
     * function that returns an array of the HTML attributes. The anonymous function will be called once for every data
     * model returned by {@see PaginatorInterface]]. The "tag" element specifies the tag name of the container element
     * and defaults to "div". If "tag" is false, it means no container element will be rendered. If this property is
     * specified as an anonymous function, it should have the following signature:
     *
     * ```php
     * function ($model, $key, $index, $widget)
     * ```
     *
     * @return $this
     *
     * {@see Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function withItemOptions($itemOptions): self
    {
        $new = clone $this;

        if (is_array($itemOptions)) {
            $new->itemOptions = ArrayHelper::merge($this->itemOptions, $itemOptions);

            return $new;
        }

        if (is_callable($itemOptions)) {
            $new->itemOptions = $itemOptions;

            return $new;
        }

        throw new InvalidArgumentException('itemOptions must be either array or callable');
    }

    /**
     * @param callable|string|null $itemView the name of the view for rendering each data item, or a callback (e.g. an
     * anonymous function) for rendering each data item. If it specifies a view name, the following variables will be
     * available in the view:
     *
     * - `$model`: mixed, the data model.
     * - `$key`: mixed, the key value associated with the data item.
     * - `$index`: integer, the zero-based index of the data item in the items array returned by
     * {@see PaginatorInterface}.
     * - `$widget`: ListView, this widget instance.
     *
     * Note that the view name is resolved into the view file by the current context of the {@see view} object.
     *
     * If this property is specified as a callback, it should have the following signature:
     * ```php
     * function ($model, $key, $index, $widget)
     * ```
     *
     * @return $this
     */
    public function withItemView($itemView): self
    {
        $new = clone $this;

        if ($itemView !== null && !is_string($itemView) && !is_callable($itemView)) {
            throw new InvalidArgumentException('itemView should be either null, string or callable');
        }

        $new->itemView = $itemView;

        return $new;
    }

    /**
     * @param string $separator the HTML code to be displayed between any two consecutive items.
     *
     * @return $this
     */
    public function withSeparator(string $separator): self
    {
        $new = clone $this;
        $new->separator = $separator;

        return $new;
    }

    /**
     * @param string $viewPath set view path for {@see ListView}.
     *
     * @return $this
     */
    public function withViewPath(string $viewPath): self
    {
        $new = clone $this;
        $new->viewPath = $viewPath;

        return $new;
    }

    /**
     * @param array $viewParams additional parameters to be passed to {@see itemView} when it is being rendered.
     *
     * This property is used only when {@see itemView} is a string representing a view name.
     *
     * @return $this
     */
    public function withViewParams(array $viewParams): self
    {
        $new = clone $this;
        $new->viewParams = $viewParams;

        return $new;
    }
}
