<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use InvalidArgumentException;
use Stringable;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

final class KeysetPagination extends Widget implements PaginationControlInterface
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

    public function withPaginator(PaginatorInterface $paginator): static
    {
        if (!$paginator instanceof KeysetPaginator) {
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

    public function disabledLinkClass(?string $class): self
    {
        $new = clone $this;
        $new->disabledLinkClass = $class;
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

    private function getPaginator(): KeysetPaginator
    {
        return $this->paginator ?? throw new PaginatorNotSetException();
    }
}
