<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use InvalidArgumentException;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\DataView\BaseListView;

/**
 * @psalm-import-type UrlCreator from BaseListView
 */
abstract class BasePagination extends Widget implements PaginationControlInterface
{
    use PaginationContextTrait;

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

    private array $linkAttributes = [];
    private ?string $currentItemClass = null;
    private ?string $currentLinkClass = null;
    private ?string $disabledItemClass = null;
    private ?string $disabledLinkClass = null;

    final public function listTag(?string $tag): static
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->listTag = $tag;
        return $new;
    }

    final public function listAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->listAttributes = $attributes;
        return $new;
    }

    final public function itemTag(?string $tag): static
    {
        if ($tag === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        $new = clone $this;
        $new->itemTag = $tag;
        return $new;
    }

    final public function itemAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->itemAttributes = $attributes;
        return $new;
    }

    final public function linkAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->linkAttributes = $attributes;
        return $new;
    }

    final public function currentItemClass(?string $class): static
    {
        $new = clone $this;
        $new->currentItemClass = $class;
        return $new;
    }

    final public function currentLinkClass(?string $class): static
    {
        $new = clone $this;
        $new->currentLinkClass = $class;
        return $new;
    }

    final public function disabledItemClass(?string $class): static
    {
        $new = clone $this;
        $new->disabledItemClass = $class;
        return $new;
    }

    final public function disabledLinkClass(?string $class): static
    {
        $new = clone $this;
        $new->disabledLinkClass = $class;
        return $new;
    }

    final public function render(): string
    {
        $result = '';

        if ($this->containerTag !== null) {
            $result .= Html::openTag($this->containerTag, $this->containerAttributes) . "\n";
        }
        if ($this->listTag !== null) {
            $result .= Html::openTag($this->listTag, $this->listAttributes) . "\n";
        }

        $renderedItems = [];
        foreach ($this->getItems() as $item) {
            $html = '';

            if ($this->itemTag !== null) {
                $attributes = $this->itemAttributes;
                if ($item->isDisabled) {
                    Html::addCssClass($attributes, $this->disabledItemClass);
                }
                if ($item->isCurrent) {
                    Html::addCssClass($attributes, $this->currentItemClass);
                }
                $html .= Html::openTag($this->itemTag, $attributes);
            }

            $linkAttributes = $this->linkAttributes;
            if ($item->isDisabled) {
                Html::addCssClass($linkAttributes, $this->disabledLinkClass);
            }
            if ($item->isCurrent) {
                Html::addCssClass($linkAttributes, $this->currentLinkClass);
            }
            $html .= Html::a($item->label, $item->url, $linkAttributes);

            if ($this->itemTag !== null) {
                $html .= Html::closeTag($this->itemTag);
            }

            $renderedItems[] = $html;
        }
        if (!empty($renderedItems)) {
            $result .= implode("\n", $renderedItems);
        }

        if ($this->listTag !== null) {
            $result .= "\n" . Html::closeTag($this->listTag);
        }
        if ($this->containerTag !== null) {
            $result .= "\n" . Html::closeTag($this->containerTag);
        }

        return $result;
    }

    /**
     * Creates the URL suitable for pagination with the specified page number. This method is mainly called by pagers
     * when creating URLs used to perform pagination.
     *
     * @param PageToken $pageToken Token for the page.
     * @return string Created URL.
     */
    protected function createUrl(PageToken $pageToken): string
    {
        $context = $this->getContext();

        if ($this->isFirstPage($pageToken)) {
            return $context->defaultUrl;
        }

        return str_replace(
            PaginationContext::URL_PLACEHOLDER,
            $pageToken->value,
            $pageToken->isPrevious ? $context->previousUrlPattern : $context->nextUrlPattern,
        );
    }

    /**
     * @return PaginationItem[]
     */
    abstract protected function getItems(): array;

    abstract protected function isFirstPage(PageToken $token): bool;
}
