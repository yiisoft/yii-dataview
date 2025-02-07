<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use InvalidArgumentException;
use Stringable;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

/**
 * Widget for rendering keyset-based pagination controls.
 *
 * Keyset pagination is a technique that uses unique keys (tokens) to navigate through
 * data sets. Unlike offset pagination, it provides consistent results even when data
 * changes between page loads. This widget renders "Previous" and "Next" links using
 * these tokens.
 *
 * Features:
 * - Customizable container, list, and item HTML structure
 * - Configurable CSS classes for disabled states
 * - Customizable navigation labels
 * - Immutable state management
 *
 * Example usage:
 * ```php
 * echo KeysetPagination::widget()
 *     ->containerTag('nav')
 *     ->listTag('ul')
 *     ->itemTag('li')
 *     ->containerAttributes(['aria-label' => 'Navigation'])
 *     ->listAttributes(['class' => 'pagination'])
 *     ->itemAttributes(['class' => 'page-item'])
 *     ->linkAttributes(['class' => 'page-link'])
 *     ->disabledItemClass('disabled')
 *     ->withPaginator($keysetPaginator)
 *     ->withContext($paginationContext);
 * ```
 *
 * The above example will render:
 * ```html
 * <nav aria-label="Navigation">
 *     <ul class="pagination">
 *         <li class="page-item disabled">
 *             <a class="page-link" href="#">⟨</a>
 *         </li>
 *         <li class="page-item">
 *             <a class="page-link" href="/items?next=token123">⟩</a>
 *         </li>
 *     </ul>
 * </nav>
 * ```
 */
final class KeysetPagination extends Widget implements PaginationWidgetInterface
{
    use PaginationContextTrait;

    private KeysetPaginator|null $paginator = null;

    /**
     * @psalm-var non-empty-string|null
     */
    private ?string $containerTag = 'nav';
    private array $containerAttributes = [];

    /**
     * @psalm-var non-empty-string|null
     */
    private ?string $listTag = null;
    private array $listAttributes = [];

    /**
     * @psalm-var non-empty-string|null
     */
    private ?string $itemTag = null;
    private array $itemAttributes = [];
    private ?string $disabledItemClass = null;

    private array $linkAttributes = [];
    private ?string $disabledLinkClass = null;

    private string|Stringable $labelPrevious = '⟨';
    private string|Stringable $labelNext = '⟩';

    /**
     * Sets the paginator instance.
     *
     * @param PaginatorInterface $paginator The paginator to use.
     * Must be an instance of {@see KeysetPaginator}.
     *
     * @throws PaginatorNotSupportedException if paginator is not a KeysetPaginator.
     *
     * @return static New instance with the specified paginator.
     */
    public function withPaginator(PaginatorInterface $paginator): static
    {
        if (!$paginator instanceof KeysetPaginator) {
            throw new PaginatorNotSupportedException($paginator);
        }

        $new = clone $this;
        $new->paginator = $paginator;
        return $new;
    }

    /**
     * Sets the container tag name.
     *
     * @param string|null $tag The tag name for the container element.
     * Common values: 'nav', 'div'. Use null to omit the container.
     *
     * @throws InvalidArgumentException if tag name is empty.
     *
     * @return self New instance with the specified container tag.
     */
    public function containerTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->containerTag = $tag;
        return $new;
    }

    /**
     * Sets the container element attributes.
     *
     * @param array $attributes HTML attributes for the container element.
     * Example: `['aria-label' => 'Navigation', 'class' => 'pagination-nav']`
     *
     * @return self New instance with the specified container attributes.
     */
    public function containerAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->containerAttributes = $attributes;
        return $new;
    }

    /**
     * Sets the list tag name.
     *
     * @param string|null $tag The tag name for the list element.
     * Common values: 'ul', 'div'. Use null to omit the list container.
     *
     * @throws InvalidArgumentException if tag name is empty.
     *
     * @return self New instance with the specified list tag.
     */
    public function listTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->listTag = $tag;
        return $new;
    }

    /**
     * Sets the list element attributes.
     *
     * @param array $attributes HTML attributes for the list element.
     * Example: `['class' => 'pagination', 'role' => 'list']`
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
     * Sets the item tag name.
     *
     * @param string|null $tag The tag name for the item elements.
     * Common values: 'li', 'div'. Use null to omit item containers.
     *
     * @throws InvalidArgumentException if tag name is empty.
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
     * Sets the item element attributes.
     *
     * @param array $attributes HTML attributes for the item elements.
     * Example: `['class' => 'page-item']`
     *
     * @return self New instance with the specified item attributes.
     */
    public function itemAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->itemAttributes = $attributes;
        return $new;
    }

    /**
     * Sets the CSS class for disabled item elements.
     *
     * @param string|null $class The CSS class for disabled items.
     * Example: 'disabled', 'inactive'
     *
     * @return self New instance with the specified disabled item class.
     */
    public function disabledItemClass(?string $class): self
    {
        $new = clone $this;
        $new->disabledItemClass = $class;
        return $new;
    }

    /**
     * Sets the link attributes.
     *
     * @param array $attributes HTML attributes for the link elements.
     * Example: `['class' => 'page-link', 'role' => 'button']`
     *
     * @return self New instance with the specified link attributes.
     */
    public function linkAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->linkAttributes = $attributes;
        return $new;
    }

    /**
     * Sets the CSS class for disabled link elements.
     *
     * @param string|null $class The CSS class for disabled links.
     * Example: 'disabled', 'inactive'
     *
     * @return self New instance with the specified disabled link class.
     */
    public function disabledLinkClass(?string $class): self
    {
        $new = clone $this;
        $new->disabledLinkClass = $class;
        return $new;
    }

    /**
     * Renders the pagination controls.
     *
     * The output includes:
     * - Optional container element (nav)
     * - Optional list element (ul)
     * - Previous and Next links with appropriate tokens
     * - Disabled states for unavailable navigation
     *
     * @return string The rendered HTML pagination controls.
     *
     * @throws PaginatorNotSetException if paginator is not set.
     */
    public function render(): string
    {
        $result = '';

        if ($this->containerTag !== null) {
            $result .= Html::openTag($this->containerTag, $this->containerAttributes) . "\n";
        }
        if ($this->listTag !== null) {
            $result .= Html::openTag($this->listTag, $this->listAttributes) . "\n";
        }

        $context = $this->getContext();
        $paginator = $this->getPaginator();
        $previousToken = $paginator->getPreviousToken();
        $nextToken = $paginator->getNextToken();
        $result .= $this->renderItem(
            $this->labelPrevious,
            $previousToken === null ? null : $context->createUrl($previousToken),
            $previousToken === null,
        )
            . "\n"
            . $this->renderItem(
                $this->labelNext,
                $nextToken === null ? null : $context->createUrl($nextToken),
                $nextToken === null,
            );

        if ($this->listTag !== null) {
            $result .= "\n" . Html::closeTag($this->listTag);
        }
        if ($this->containerTag !== null) {
            $result .= "\n" . Html::closeTag($this->containerTag);
        }

        return $result;
    }

    /**
     * Renders a single pagination item (Previous or Next).
     *
     * @param string|Stringable $label The item label.
     * @param string|null $url The item URL, or null if disabled.
     * @param bool $isDisabled Whether the item should be rendered as disabled.
     *
     * @return Stringable The rendered HTML for the pagination item.
     */
    private function renderItem(string|Stringable $label, ?string $url, bool $isDisabled): Stringable
    {
        $linkAttributes = $this->linkAttributes;
        if ($isDisabled) {
            Html::addCssClass($linkAttributes, $this->disabledLinkClass);
        }
        $link = Html::a($label, $url, $linkAttributes);

        if ($this->itemTag === null) {
            return $link;
        }

        $attributes = $this->itemAttributes;
        if ($isDisabled) {
            Html::addCssClass($attributes, $this->disabledItemClass);
        }
        return Html::tag($this->itemTag, $link, $attributes);
    }

    /**
     * Gets the keyset paginator instance.
     *
     * @throws PaginatorNotSetException if paginator is not set.
     *
     * @return KeysetPaginator The keyset paginator instance.
     */
    private function getPaginator(): KeysetPaginator
    {
        return $this->paginator ?? throw new PaginatorNotSetException();
    }
}
