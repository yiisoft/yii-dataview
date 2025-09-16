<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use BackedEnum;
use InvalidArgumentException;
use Stringable;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

use function max;
use function min;

/**
 * Widget for rendering {@see OffsetPaginator}.
 *
 * @implements PaginationWidgetInterface<OffsetPaginator>
 */
final class OffsetPagination extends Widget implements PaginationWidgetInterface
{
    use PaginationContextTrait;

    private OffsetPaginator|null $paginator = null;

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
    private ?string $currentItemClass = null;
    private ?string $disabledItemClass = null;

    private array $linkAttributes = [];
    private ?string $currentLinkClass = null;
    private ?string $disabledLinkClass = null;

    private string|Stringable|null $labelPrevious = '⟨';
    private string|Stringable|null $labelNext = '⟩';
    private string|Stringable|null $labelFirst = '⟪';
    private string|Stringable|null $labelLast = '⟫';

    private int $maxNavLinkCount = 10;

    /**
     * Creates a new instance with the specified paginator and context.
     *
     * @param OffsetPaginator $paginator The paginator to use.
     * @param string $urlPattern URL pattern for page links. Must contain {@see PaginationContext::URL_PLACEHOLDER}.
     * @param string $firstPageUrl URL used on the first page.
     *
     * @return self New instance with the specified paginator and context.
     */
    public static function create(OffsetPaginator $paginator, string $urlPattern, string $firstPageUrl): self
    {
        return self::widget()
            ->withPaginator($paginator)
            ->withContext(
                new PaginationContext($urlPattern, $urlPattern, $firstPageUrl),
            );
    }

    /**
     * @inheritDoc
     *
     * @throws PaginatorNotSupportedException If paginator is not a {@see OffsetPaginator}.
     */
    public function withPaginator(PaginatorInterface $paginator): static
    {
        /** @psalm-suppress DocblockTypeContradiction, NoValue */
        if (!$paginator instanceof OffsetPaginator) {
            throw new PaginatorNotSupportedException($paginator);
        }

        $new = clone $this;
        $new->paginator = $paginator;
        return $new;
    }

    public function containerTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->containerTag = $tag;
        return $new;
    }

    public function containerAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->containerAttributes = $attributes;
        return $new;
    }

    public function listTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->listTag = $tag;
        return $new;
    }

    public function listAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->listAttributes = $attributes;
        return $new;
    }

    public function itemTag(?string $tag): self
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->itemTag = $tag;
        return $new;
    }

    public function itemAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->itemAttributes = $attributes;
        return $new;
    }

    public function currentItemClass(?string $class): self
    {
        $new = clone $this;
        $new->currentItemClass = $class;
        return $new;
    }

    public function disabledItemClass(?string $class): self
    {
        $new = clone $this;
        $new->disabledItemClass = $class;
        return $new;
    }

    public function linkAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->linkAttributes = $attributes;
        return $new;
    }

    public function addLinkAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->linkAttributes = array_merge($new->linkAttributes, $attributes);
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

    public function currentLinkClass(?string $class): self
    {
        $new = clone $this;
        $new->currentLinkClass = $class;
        return $new;
    }

    public function disabledLinkClass(?string $class): self
    {
        $new = clone $this;
        $new->disabledLinkClass = $class;
        return $new;
    }

    public function labelPrevious(string|Stringable|null $label): self
    {
        $new = clone $this;
        $new->labelPrevious = $label;
        return $new;
    }

    public function labelNext(string|Stringable|null $label): self
    {
        $new = clone $this;
        $new->labelNext = $label;
        return $new;
    }

    public function labelFirst(string|Stringable|null $label): self
    {
        $new = clone $this;
        $new->labelFirst = $label;
        return $new;
    }

    public function labelLast(string|Stringable|null $label): self
    {
        $new = clone $this;
        $new->labelLast = $label;
        return $new;
    }

    /**
     * Return a new instance with a max nav link count.
     *
     * @param int $value Max nav link count.
     */
    public function maxNavLinkCount(int $value): self
    {
        $new = clone $this;
        $new->maxNavLinkCount = $value;
        return $new;
    }

    public function render(): string
    {
        $result = '';

        if ($this->containerTag !== null) {
            $result .= Html::openTag($this->containerTag, $this->containerAttributes) . "\n";
        }
        if ($this->listTag !== null) {
            $result .= Html::openTag($this->listTag, $this->listAttributes) . "\n";
        }

        $items = $this->renderItems();

        $result .= implode("\n", $items);

        if ($this->listTag !== null) {
            $result .= "\n" . Html::closeTag($this->listTag);
        }
        if ($this->containerTag !== null) {
            $result .= "\n" . Html::closeTag($this->containerTag);
        }

        return $result;
    }

    /**
     * @return list<Stringable>
     */
    private function renderItems(): array
    {
        $paginator = $this->getPaginator();
        $currentPage = $paginator->getCurrentPage();
        $totalPages = $paginator->getTotalPages();
        [$beginPage, $endPage] = $this->getPageRange($currentPage, $totalPages);

        $items = [];

        if ($this->labelFirst !== null) {
            $items[] = $this->renderItem(
                label: $this->labelFirst,
                url: $this->createUrl(PageToken::next('1')),
                isCurrent: false,
                isDisabled: $currentPage === 1,
            );
        }

        if ($this->labelPrevious !== null) {
            $items[] = $this->renderItem(
                label: $this->labelPrevious,
                url: $this->createUrl(PageToken::next((string) max($currentPage - 1, 1))),
                isCurrent: false,
                isDisabled: $currentPage === 1,
            );
        }

        $page = $beginPage;
        do {
            $items[] = $this->renderItem(
                label: (string) $page,
                url: $this->createUrl(PageToken::next((string) $page)),
                isCurrent: $page === $currentPage,
                isDisabled: false,
            );
        } while (++$page <= $endPage);

        if ($this->labelNext !== null) {
            $items[] = $this->renderItem(
                label: $this->labelNext,
                url: $this->createUrl(PageToken::next((string) min($currentPage + 1, $totalPages))),
                isCurrent: false,
                isDisabled: $currentPage === $totalPages,
            );
        }

        if ($this->labelLast !== null) {
            $items[] = $this->renderItem(
                label: $this->labelLast,
                url: $this->createUrl(PageToken::next((string) $totalPages)),
                isCurrent: false,
                isDisabled: $currentPage === $totalPages,
            );
        }

        return $items;
    }

    private function renderItem(string|Stringable $label, string $url, bool $isCurrent, bool $isDisabled): Stringable
    {
        $linkAttributes = $this->linkAttributes;
        if ($isDisabled) {
            Html::addCssClass($linkAttributes, $this->disabledLinkClass);
        }
        if ($isCurrent) {
            Html::addCssClass($linkAttributes, $this->currentLinkClass);
        }
        $link = Html::a($label, $url, $linkAttributes);

        if ($this->itemTag === null) {
            return $link;
        }

        $attributes = $this->itemAttributes;
        if ($isDisabled) {
            Html::addCssClass($attributes, $this->disabledItemClass);
        }
        if ($isCurrent) {
            Html::addCssClass($attributes, $this->currentItemClass);
        }
        return Html::tag($this->itemTag, $link, $attributes);
    }

    /**
     * @psalm-return array<int, int>
     */
    private function getPageRange(int $currentPage, int $totalPages): array
    {
        $beginPage = max(1, $currentPage - (int) ($this->maxNavLinkCount / 2));

        if (($endPage = $beginPage + $this->maxNavLinkCount - 1) >= $totalPages) {
            $endPage = $totalPages;
            $beginPage = max(1, $endPage - $this->maxNavLinkCount + 1);
        }

        if ($totalPages !== 0 && $currentPage > $totalPages) {
            throw new InvalidArgumentException('Current page must be less than or equal to total pages.');
        }

        return [$beginPage, $endPage];
    }

    /**
     * @param PageToken $pageToken Token for the page.
     * @return string Created URL.
     */
    private function createUrl(PageToken $pageToken): string
    {
        $context = $this->getContext();
        return $pageToken->value === '1'
            ? $context->firstPageUrl
            : $context->createUrl($pageToken);
    }

    private function getPaginator(): OffsetPaginator
    {
        return $this->paginator ?? throw new PaginatorNotSetException();
    }
}
