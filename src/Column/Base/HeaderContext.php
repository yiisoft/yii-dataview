<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Stringable;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\A;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\BaseListView;
use Yiisoft\Yii\DataView\UrlConfig;
use Yiisoft\Yii\DataView\UrlParametersFactory;

/**
 * @psalm-import-type UrlCreator from BaseListView
 */
final class HeaderContext
{
    /**
     * @internal
     *
     * @psalm-param UrlCreator|null $urlCreator
     */
    public function __construct(
        private readonly ?Sort $originalSort,
        private readonly ?Sort $sort,
        private readonly ?string $sortableHeaderClass,
        private string|Stringable $sortableHeaderPrepend,
        private string|Stringable $sortableHeaderAppend,
        private readonly ?string $sortableHeaderAscClass,
        private readonly string|Stringable $sortableHeaderAscPrepend,
        private readonly string|Stringable $sortableHeaderAscAppend,
        private readonly ?string $sortableHeaderDescClass,
        private readonly string|Stringable $sortableHeaderDescPrepend,
        private readonly string|Stringable $sortableHeaderDescAppend,
        public readonly array $sortableLinkAttributes,
        public readonly ?string $sortableLinkAscClass,
        public readonly ?string $sortableLinkDescClass,
        private readonly ?PageToken $pageToken,
        private readonly int|null $pageSize,
        private readonly bool $enableMultiSort,
        private readonly UrlConfig $urlConfig,
        private $urlCreator,
        private readonly TranslatorInterface $translator,
        private readonly string $translationCategory,
    ) {
    }

    public function translate(string|Stringable $id): string
    {
        return $this->translator->translate($id, category: $this->translationCategory);
    }

    /**
     * @psalm-return list{Cell,?A,string,string}
     */
    public function prepareSortable(Cell $cell, string $property): array
    {
        if ($this->sort === null || $this->originalSort === null || !$this->sort->hasFieldInConfig($property)) {
            return [$cell, null, '', ''];
        }

        $linkAttributes = [];
        $propertyOrder = $this->sort->getOrder()[$property] ?? null;
        if ($propertyOrder === null) {
            $cell = $cell->addClass($this->sortableHeaderClass);
            $prepend = $this->sortableHeaderPrepend;
            $append = $this->sortableHeaderAppend;
        } else {
            $cell = $cell->addClass(
                $propertyOrder === 'asc' ? $this->sortableHeaderAscClass : $this->sortableHeaderDescClass
            );
            $prepend = $propertyOrder === 'asc' ? $this->sortableHeaderAscPrepend : $this->sortableHeaderDescPrepend;
            $append = $propertyOrder === 'asc' ? $this->sortableHeaderAscAppend : $this->sortableHeaderDescAppend;
            Html::addCssClass(
                $linkAttributes,
                $propertyOrder === 'asc' ? $this->sortableLinkAscClass : $this->sortableLinkDescClass,
            );
        }
        $url = $this->urlCreator === null ? '#' : call_user_func_array(
            $this->urlCreator,
            UrlParametersFactory::create(
                $this->pageToken,
                $this->pageSize,
                $this->getLinkSortValue($this->originalSort, $this->sort, $property),
                $this->urlConfig,
            )
        );

        return [
            $cell,
            A::tag()->attributes($linkAttributes)->url($url),
            (string) $prepend,
            (string) $append,
        ];
    }

    private function getLinkSortValue(Sort $originalSort, Sort $sort, string $property): ?string
    {
        $originalOrder = $originalSort->getOrder();
        $order = $sort->getOrder();

        if (isset($order[$property])) {
            if ($this->enableMultiSort) {
                if ($order[$property] === 'asc') {
                    $order[$property] = 'desc';
                } else {
                    if (count($order) === 1 && !empty($originalOrder)) {
                        $order[$property] = 'asc';
                    } else {
                        unset($order[$property]);
                    }
                }
            } else {
                if (isset($originalOrder[$property])) {
                    if ($order[$property] === $originalOrder[$property]) {
                        $order = [$property => $originalOrder[$property] === 'asc' ? 'desc' : 'asc'];
                    } else {
                        unset($order[$property]);
                    }
                } else {
                    if ($order[$property] === 'asc') {
                        $order = [$property => 'desc'];
                    } else {
                        unset($order[$property]);
                    }
                }
            }
        } else {
            if ($this->enableMultiSort) {
                $order[$property] = 'asc';
            } else {
                $order = [$property => 'asc'];
            }
        }

        if ($this->isEqualOrders($order, $originalOrder)) {
            return null;
        }

        $result = $sort->withOrder($order)->getOrderAsString();
        if (empty($result)) {
            return null;
        }

        return $result;
    }

    private function isEqualOrders(array $a, array $b): bool
    {
        ksort($a);
        ksort($b);
        return $a === $b;
    }
}
