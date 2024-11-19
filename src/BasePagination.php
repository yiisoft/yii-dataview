<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use InvalidArgumentException;
use LogicException;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Data\Reader\OrderHelper;
use Yiisoft\Html\Html;
use Yiisoft\Http\Method;
use Yiisoft\Widget\Widget;

use function array_key_exists;
use function call_user_func_array;

/**
 * @psalm-import-type UrlCreator from BaseListView
 */
abstract class BasePagination extends Widget
{
    private ?PaginationContext $context = null;

    /**
     * @var array|bool|int Page size constraint.
     *  - `true` - default only.
     *  - `false` - no constraint.
     *  - int - maximum page size.
     *  -  [int, int, ...] - a list of page sizes to choose from.
     */
    private bool|int|array $pageSizeConstraint = true;

    /**
     * @psalm-var UrlCreator|null
     */
    private $urlCreator;
    private UrlConfig $urlConfig;

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

    final public function context(?PaginationContext $context): static
    {
        $new = clone $this;
        $new->context = $context;
        return $new;
    }

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

        $result .= $this->renderPageSize();

        return $result;
    }

    private function renderPageSize(): string
    {
        $out = Html::openTag('nav', ['class' => 'pageSize']);

        $form = Html::form($this->getPageSizeItem()->url, Method::GET);
        $out .= $form->open();

        if (is_int($this->pageSizeConstraint)) {
            $out .= Html::textInput(
                $this->urlConfig->getPageSizeParameterName(),
                $this->getPaginator()->getPageSize(),
                ['class' => 'form-control']
            );
        } elseif (is_array($this->pageSizeConstraint)) {
            $options = array_combine($this->pageSizeConstraint, $this->pageSizeConstraint);
            $out .= Html::select($this->urlConfig->getPageSizeParameterName())
                ->optionsData($options)
                ->value($this->getPaginator()->getPageSize());
        } else {
            return '';
        }

        $out .= Html::submitButton();
        $out .= $form->close();

        $out .= Html::closeTag('nav');

        return $out;
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

    public function urlConfig(UrlConfig $config): static
    {
        $new = clone $this;
        $new->urlConfig = $config;
        return $new;
    }

    /**
     * Creates the URL suitable for pagination with the specified page number. This method is mainly called by pagers
     * when creating URLs used to perform pagination.
     *
     * @param PageToken $pageToken Token for the page.
     * @param int|null $pageSize Page size to change to.
     * @return string Created URL.
     */
    protected function createUrl(PageToken $pageToken, ?int $pageSize = null): string
    {
        if ($this->urlCreator === null) {
            return '#' . $pageToken->value;
        }

        $paginator = $this->getPaginator();
        $pageSize ??= $paginator->getPageSize();
        $sort = $this->getSort($paginator, $this->context);

        return call_user_func_array(
            $this->urlCreator,
            UrlParametersFactory::create(
                $this->isFirstPage($pageToken) ? null : $pageToken,
                $pageSize === $this->defaultPageSize ? null : $pageSize,
                empty($sort) ? null : $sort,
                $this->urlConfig,
            )
        );
    }

    private function getPageSizeItem(): PaginationItem
    {
        $first = null;
        foreach ($this->getItems() as $item) {
            if ($first === null) {
                $first = $item;
            }
            if ($item->isCurrent) {
                return $item;
            }
        }
        if ($first !== null) {
            return $first;
        }

        throw new LogicException('Page size item is missing.');
    }

    /**
     * Get page size constraint.
     *
     * @return array|bool|int Page size constraint.
     * - `true` - default only.
     * - `false` - no constraint.
     * - int - maximum page size.
     * -  [int, int, ...] - a list of page sizes to choose from.
     */
    public function getPageSizeConstraint(): array|int|bool
    {
        return $this->pageSizeConstraint;
    }

    /**
     * Get a new instance with a page size constraint set.
     *
     * @param array|bool|int $pageSizeConstraint Page size constraint.
     * `true` - default only.
     * `false` - no constraint.
     * int - maximum page size.
     * [int, int, ...] - a list of page sizes to choose from.
     * @return static New instance.
     */
    public function pageSizeConstraint(array|int|bool $pageSizeConstraint): static
    {
        $new = clone $this;
        $new->pageSizeConstraint = $pageSizeConstraint;
        return $new;
    }

    /**
     * @return PaginationItem[]
     */
    abstract protected function getItems(): array;

    abstract protected function getPaginator(): PaginatorInterface;

    abstract protected function isFirstPage(PageToken $token): bool;

    private function getSort(PaginatorInterface $paginator, ?PaginationContext $context): ?string
    {
        if (!$paginator->isSortable()) {
            return null;
        }

        $sort = $paginator->getSort();
        if ($sort === null) {
            return null;
        }

        if ($context === null) {
            return $sort->getOrderAsString();
        }

        $order = [];
        $overrideOrderFields = array_flip($context->overrideOrderFields);
        foreach ($sort->getOrder() as $name => $value) {
            $key = array_key_exists($name, $overrideOrderFields)
                ? $overrideOrderFields[$name]
                : $name;
            $order[$key] = $value;
        }

        return OrderHelper::arrayToString($order);
    }
}
