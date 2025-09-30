<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\ListView;

use Closure;
use InvalidArgumentException;
use LogicException;
use Stringable;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result as ValidationResult;
use Yiisoft\View\Exception\ViewNotFoundException;
use Yiisoft\View\View;
use Yiisoft\Yii\DataView\BaseListView;

use function count;
use function is_callable;
use function is_string;

/**
 * `ListView` is a flexible widget for displaying a list of data items with customizable rendering and layout.
 *
 * Example usage:
 * ```php
 * $listView = (new ListView())
 *     ->listTag('ul')
 *     ->listAttributes(['class' => 'my-list'])
 *     ->itemTag('li')
 *     ->itemView(fn($data, $context) => Html::encode($data['name']))
 *     ->dataReader($dataReader);
 *
 * echo $listView->render();
 * ```
 *
 * @psalm-type ItemAffixClosure = Closure(array|object, ListItemContext): (string|Stringable)
 * @psalm-type ItemViewClosure = Closure(array|object, ListItemContext): (string|Stringable)
 * @psalm-type ItemAttributesClosure = Closure(array|object, ListItemContext): array
 */
final class ListView extends BaseListView
{
    /** @psalm-var non-empty-string|null */
    private string|null $listTag = 'ul';
    private array $listAttributes = [];

    /** @psalm-var non-empty-string|null */
    private string|null $itemTag = 'li';
    /** @psalm-var array|ItemAttributesClosure */
    private array|Closure $itemAttributes = [];

    /** @psalm-var ItemAffixClosure|string */
    private Closure|string $beforeItem = '';
    /** @psalm-var ItemAffixClosure|string */
    private Closure|string $afterItem = '';
    /** @psalm-var string|ItemViewClosure|null */
    private string|Closure|null $itemView = null;
    private array $itemViewParameters = [];

    /** @psalm-var non-empty-string|null */
    private string|null $noResultsTag = 'p';
    private array $noResultsAttributes = [];

    private string $separator = "\n";

    private readonly View $view;

    public function __construct(
        TranslatorInterface|null $translator = null,
        string $translationCategory = self::DEFAULT_TRANSLATION_CATEGORY,
    ) {
        $this->view = new View();
        parent::__construct($translator, $translationCategory);
    }

    /**
     * Set the HTML tag for the list.
     *
     * @param string|null $tag The tag name. If `null`, no tag will be rendered.
     *
     * @return self New instance with the specified list tag.
     */
    public function listTag(string|null $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->listTag = $tag;
        return $new;
    }

    /**
     * Set the HTML attributes for the list.
     *
     * @param array $attributes Attribute values indexed by attribute names.
     *
     * @return self New instance with the specified list attributes.
     */
    public function listAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->listAttributes = $attributes;
        return $new;
    }

    /**
     * Set the HTML tag for the list item.
     *
     * @param string|null $tag The tag name. If `null`, no tag will be rendered.
     *
     * @return self New instance with the specified item tag.
     */
    public function itemTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
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
     *
     * @psalm-param ItemAttributesClosure|array $attributes
     */
    public function itemAttributes(array|Closure $attributes): self
    {
        $new = clone $this;
        $new->itemAttributes = $attributes;
        return $new;
    }

    /**
     * Return new instance with a string or a function that is called before rendering each item.
     *
     * @param Closure|string $value A string or a callback with the following signature:
     *
     * ```php
     * function (ListItemContext $context): string|Stringable
     * ```
     *
     * The return result of the function will be rendered directly.
     *
     * @return self New instance.
     *
     * @psalm-param ItemAffixClosure|string $value
     *
     * @see renderBeforeItem()
     */
    public function beforeItem(Closure|string $value): self
    {
        $new = clone $this;
        $new->beforeItem = $value;
        return $new;
    }

    /**
     * Return new instance with a string or a function that is called after rendering each item.
     *
     * @param Closure|string $value A string or a callback with the following signature:
     *
     * ```php
     * function (ListItemContext $context): string|Stringable
     * ```
     *
     * The return result of the function will be rendered directly.
     *
     * @return self New instance.
     *
     * @psalm-param ItemAffixClosure|string $value
     *
     * @see renderAfterItem()
     */
    public function afterItem(Closure|string $value): self
    {
        $new = clone $this;
        $new->afterItem = $value;
        return $new;
    }

    /**
     * Return new instance with the content rendering configuration for each list item.
     *
     * @param Closure|string $value Either a view file path (string) or a closure for rendering item content.
     *
     * If a string is provided, it should be a path to a view file. The view will receive:
     * - `$data` - the current item data;
     * - `$context` - the {@see ListItemContext} instance;
     * - any additional parameters set via {@see itemViewParameters()}.
     *
     * If a closure is provided, it should have the following signature:
     * ```php
     * function (array|object $data, ListItemContext $context): string|Stringable
     * ```
     *
     * @return self New instance.
     *
     * @psalm-param string|ItemViewClosure $value
     */
    public function itemView(string|Closure $value): self
    {
        $new = clone $this;
        $new->itemView = $value;
        return $new;
    }

    /**
     * Return new instance with the additional parameters for the view.
     *
     * @param array $parameters Additional parameters to be passed to the view set in {@see itemView()} when it is
     * being rendered.
     *
     * @return self New instance with the specified view parameters.
     */
    public function itemViewParameters(array $parameters): self
    {
        $new = clone $this;
        $new->itemViewParameters = $parameters;
        return $new;
    }

    public function noResultsTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->noResultsTag = $tag;
        return $new;
    }

    public function noResultsAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->noResultsAttributes = $attributes;
        return $new;
    }

    /**
     * Return new instance with the separator between the items.
     *
     * @param string $separator The HTML code to be displayed between any two consecutive items.
     *
     * @return self New instance with the specified separator.
     */
    public function separator(string $separator): self
    {
        $new = clone $this;
        $new->separator = $separator;
        return $new;
    }

    protected function makeFilters(): array
    {
        return [[], new ValidationResult()];
    }

    protected function prepareOrder(array $order): array
    {
        return [];
    }

    protected function renderItems(
        array $items,
        ValidationResult $filterValidationResult,
        ReadableDataInterface|null $preparedDataReader,
    ): string {
        if (empty($items)) {
            return $this->renderNoResults();
        }

        $rows = [];
        foreach ($items as $key => $value) {
            $context = new ListItemContext($value, $key, count($rows), $this);
            $rows[] = $this->renderBeforeItem($context)
                . $this->renderItem($context)
                . $this->renderAfterItem($context);
        }

        $content = implode($this->separator, $rows);

        return $this->listTag === null
            ? $content
            : Html::tag($this->listTag, "\n" . $content . "\n", $this->listAttributes)
                ->encode(false)
                ->render();
    }

    /**
     * Renders a single list item.
     *
     * @param ListItemContext $context The context.
     *
     * @throws ViewNotFoundException If the item view file doesn't exist.
     * @throws LogicException If {@see itemView} isn't set.
     *
     * @return string The rendered HTML for the list item.
     */
    private function renderItem(ListItemContext $context): string
    {
        $content = $this->renderItemContent($context);
        if ($this->itemTag === null) {
            return $content;
        }

        $attributes = $this->prepareItemAttributes($context);

        return Html::tag($this->itemTag)
                ->attributes($attributes)
                ->content($content)
                ->encode(false)
                ->render();
    }

    private function prepareItemAttributes(ListItemContext $context): array
    {
        $attributes = is_callable($this->itemAttributes)
            ? ($this->itemAttributes)($context->data, $context)
            : $this->itemAttributes;

        return array_map(
            static fn(mixed $attribute): mixed => is_callable($attribute) ? $attribute($context) : $attribute,
            $attributes,
        );
    }

    /**
     * @param ListItemContext $context The context containing item data and rendering information.
     *
     * @throws ViewNotFoundException If the item view file doesn't exist.
     * @throws LogicException If {@see $itemView} is not configured.
     *
     * @return string The rendered item content HTML.
     */
    private function renderItemContent(ListItemContext $context): string
    {
        if ($this->itemView === null) {
            throw new LogicException('"itemView" must be set.');
        }

        if (is_string($this->itemView)) {
            return $this->view->render(
                $this->itemView,
                array_merge(
                    $this->itemViewParameters,
                    [
                        'data' => $context->data,
                        'context' => $context,
                    ],
                )
            );
        }

        return (string) ($this->itemView)($context->data, $context);
    }

    /**
     * Renders content that appears before each list item.
     *
     * @param ListItemContext $context Context of the item to be rendered.
     *
     * @return string The rendered before-item content.
     */
    private function renderBeforeItem(ListItemContext $context): string
    {
        return is_string($this->beforeItem)
            ? $this->beforeItem
            : (string) ($this->beforeItem)($context->data, $context);
    }

    /**
     * Renders content that appears after each list item.
     *
     * @param ListItemContext $context Context of the item to be rendered.
     *
     * @return string The rendered after-item content.
     */
    private function renderAfterItem(ListItemContext $context): string
    {
        return is_string($this->afterItem)
            ? $this->afterItem
            : (string) ($this->afterItem)($context->data, $context);
    }

    private function renderNoResults(): string
    {
        $text = $this->getNoResultsContent();
        if ($text === '') {
            return '';
        }

        return $this->noResultsTag === null
            ? $text
            : Html::tag($this->noResultsTag, $text, $this->noResultsAttributes)
                ->encode(false)
                ->render();
    }
}
