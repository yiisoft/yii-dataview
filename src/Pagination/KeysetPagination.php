<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use BackedEnum;
use InvalidArgumentException;
use Stringable;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

/**
 * Widget for rendering {@see KeysetPaginator}.
 *
 * @implements PaginationWidgetInterface<KeysetPaginator>
 */
final class KeysetPagination extends Widget implements PaginationWidgetInterface
{
    use PaginationContextTrait;

    private KeysetPaginator|null $paginator = null;

    private bool $showOnSinglePage = false;

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
     * Creates a new instance with the specified paginator and context.
     *
     * @param KeysetPaginator $paginator The paginator to use.
     * @param string $nextUrlPattern URL pattern for next page links. Must contain {@see PaginationContext::URL_PLACEHOLDER}.
     * @param string $previousUrlPattern URL pattern for previous page links. Must contain {@see PaginationContext::URL_PLACEHOLDER}.
     *
     * @return self New instance with the specified paginator and context.
     */
    public static function create(KeysetPaginator $paginator, string $nextUrlPattern, string $previousUrlPattern): self
    {
        return self::widget()
            ->withPaginator($paginator)
            ->withContext(
                new PaginationContext($nextUrlPattern, $previousUrlPattern, ''),
            );
    }

    /**
     * @inheritDoc
     *
     * @throws PaginatorNotSupportedException If paginator is not a {@see KeysetPaginator}.
     */
    public function withPaginator(PaginatorInterface $paginator): static
    {
        /** @psalm-suppress DocblockTypeContradiction, NoValue */
        if (!$paginator instanceof KeysetPaginator) {
            throw new PaginatorNotSupportedException($paginator);
        }

        $new = clone $this;
        $new->paginator = $paginator;
        return $new;
    }

    public function showOnSinglePage(bool $show = true): self
    {
        $this->showOnSinglePage = $show;
    }

    /**
     * Sets the container tag name.
     *
     * @param string|null $tag The tag name for the container element.
     * Common values: 'nav', 'div'. Use `null` to omit the container.
     *
     * @throws InvalidArgumentException If tag name is empty.
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
     * Common values: 'ul', 'div'. Use `null` to omit the list container.
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
     * Common values: 'li', 'div'. Use `null` to omit item containers.
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
     * Set new link classes.
     *
     * Multiple classes can be set by passing them as separate arguments. `null` values are filtered out
     * automatically.
     *
     * @param BackedEnum|string|null ...$class One or more CSS class names to use. Pass `null` to skip a class.
     * @return self
     */
    public function linkClass(BackedEnum|string|null ...$class): self
    {
        $new = clone $this;
        $new->linkAttributes['class'] = [];
        Html::addCssClass($new->linkAttributes, $class);
        return $new;
    }

    /**
     * Adds one or more CSS classes to the existing link classes.
     *
     * Multiple classes can be added by passing them as separate arguments. `null` values are filtered out
     * automatically.
     *
     * @param BackedEnum|string|null ...$class One or more CSS class names to add. Pass `null` to skip adding a class.
     * @return self A new instance with the specified CSS classes added to existing ones.
     */
    public function addLinkClass(BackedEnum|string|null ...$class): self
    {
        $new = clone $this;
        Html::addCssClass($new->linkAttributes, $class);
        return $new;
    }

    /**
     * Sets the CSS class for disabled link elements.
     *
     * @param string|null $class The CSS class for disabled links.
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
     * @throws PaginatorNotSetException If paginator is not set.
     * @return string The rendered HTML pagination controls.
     */
    public function render(): string
    {
        if (!$this->showOnSinglePage && !$this->getPaginator()->isPaginationRequired()) {
            return '';
        }

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
     * Renders a single pagination item (previous or next).
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
     * @throws PaginatorNotSetException If paginator is not set.
     *
     * @return KeysetPaginator The keyset paginator instance.
     */
    private function getPaginator(): KeysetPaginator
    {
        return $this->paginator ?? throw new PaginatorNotSetException();
    }
}
