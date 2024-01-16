<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use InvalidArgumentException;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

/**
 * @psalm-type UrlCreator = callable(PageContext):string
 */
abstract class BasePagination extends Widget
{
    /**
     * @psalm-var UrlCreator|null
     */
    private $urlCreator;
    private string $pageParameterName = 'page';
    private string $previousPageParameterName = 'previous-page';
    private string $pageSizeParameterName = 'pagesize';

    /**
     * @psalm-var UrlParameterType::*
     */
    private int $pageParameterType = UrlParameterType::QUERY;

    /**
     * @psalm-var UrlParameterType::*
     */
    private int $previousPageParameterType = UrlParameterType::QUERY;

    /**
     * @psalm-var UrlParameterType::*
     */
    private int $pageSizeParameterType = UrlParameterType::QUERY;

    private int $defaultPageSize = PaginatorInterface::DEFAULT_PAGE_SIZE;

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

    private array $queryParameters = [];

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

            $attributes = $this->linkAttributes;
            if ($item->isDisabled) {
                Html::addCssClass($attributes, $this->disabledLinkClass);
            }
            if ($item->isCurrent) {
                Html::addCssClass($attributes, $this->currentLinkClass);
            }
            $html .= Html::a($item->label, $item->url, $attributes);

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
     * @psalm-param UrlCreator|null $urlCreator
     */
    public function urlCreator(?callable $urlCreator): static
    {
        $new = clone $this;
        $new->urlCreator = $urlCreator;
        return $new;
    }

    final public function defaultPageSize(int $size): static
    {
        $new = clone $this;
        $new->defaultPageSize = $size;
        return $new;
    }

    /**
     * Return a new instance with query parameters of the route.
     *
     * @param array $value The query parameters of the route.
     */
    public function queryParameters(array $value): static
    {
        $new = clone $this;
        $new->queryParameters = $value;
        return $new;
    }

    /**
     * Return a new instance with name of argument or query parameter for page.
     *
     * @param string $name The name of argument or query parameter for page.
     */
    public function pageParameterName(string $name): static
    {
        $new = clone $this;
        $new->pageParameterName = $name;
        return $new;
    }

    public function previousPageParameterName(string $name): static
    {
        $new = clone $this;
        $new->previousPageParameterName = $name;
        return $new;
    }

    /**
     * Return a new instance with name of argument or query parameter for page size.
     *
     * @param string $name The name of argument or query parameter for page size.
     */
    public function pageSizeParameterName(string $name): static
    {
        $new = clone $this;
        $new->pageSizeParameterName = $name;

        return $new;
    }

    /**
     * Creates the URL suitable for pagination with the specified page number. This method is mainly called by pagers
     * when creating URLs used to perform pagination.
     */
    protected function createUrl(int|string $page, bool $isPrevious = false): string
    {
        if ($this->urlCreator === null) {
            return '#' . $page;
        }

        return call_user_func(
            $this->urlCreator,
            new PageContext(
                $page,
                $this->getPaginator()->getPageSize(),
                $isPrevious,
                $this->isFirstPage($page, $isPrevious),
                $this->pageParameterName,
                $this->previousPageParameterName,
                $this->pageSizeParameterName,
                $this->pageParameterType,
                $this->previousPageParameterType,
                $this->pageSizeParameterType,
                $this->queryParameters,
                $this->defaultPageSize,
            )
        );
    }

    /**
     * @return PaginationItem[]
     */
    abstract protected function getItems(): array;

    abstract protected function getPaginator(): PaginatorInterface;

    abstract protected function isFirstPage(int|string $page, bool $isPrevious): bool;
}
