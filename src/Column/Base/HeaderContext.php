<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Stringable;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Data\Reader\SortableDataInterface;
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
        public readonly ReadableDataInterface $dataReader,
        private ReadableDataInterface $preparedDataReader,
        private readonly ?string $sortableHeaderClass,
        private string|Stringable $sortableHeaderPrepend,
        private string|Stringable $sortableHeaderAppend,
        private ?string $sortableHeaderAscClass,
        private string|Stringable $sortableHeaderAscPrepend,
        private string|Stringable $sortableHeaderAscAppend,
        private ?string $sortableHeaderDescClass,
        private string|Stringable $sortableHeaderDescPrepend,
        private string|Stringable $sortableHeaderDescAppend,
        public readonly array $sortableLinkAttributes,
        public readonly ?string $sortableLinkAscClass,
        public readonly ?string $sortableLinkDescClass,
        private ?PageToken $pageToken,
        private int|null $pageSize,
        private bool $enableMultiSort,
        private UrlConfig $urlConfig,
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
        $sort = $this->getSort($this->preparedDataReader);
        if ($sort === null || !$sort->hasFieldInConfig($property)) {
            return [$cell, null, '', ''];
        }

        $linkAttributes = [];
        $propertyOrder = $sort->getOrder()[$property] ?? null;
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
                $this->getLinkSortValue($sort, $property),
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

    private function getLinkSortValue(Sort $sort, string $property): ?string
    {
        $order = $sort->getOrder();

        if (isset($order[$property])) {
            if ($order[$property] === 'asc') {
                if ($this->enableMultiSort) {
                    $order[$property] = 'desc';
                } else {
                    $order = [$property => 'desc'];
                }
            } else {
                unset($order[$property]);
            }
        } else {
            if ($this->enableMultiSort) {
                $order[$property] = 'asc';
            } else {
                $order = [$property => 'asc'];
            }
        }

        $result = $sort->withOrder($order)->getOrderAsString();

        return empty($result) ? null : $result;
    }

    private function getSort(ReadableDataInterface $dataReader): ?Sort
    {
        if ($dataReader instanceof PaginatorInterface && $dataReader->isSortable()) {
            return $dataReader->getSort();
        }

        if ($dataReader instanceof SortableDataInterface) {
            return $dataReader->getSort();
        }

        return null;
    }
}
