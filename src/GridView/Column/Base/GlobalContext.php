<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\GridView\Column\Base;

use Stringable;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Reader\OrderHelper;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\A;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\BaseListView;
use Yiisoft\Yii\DataView\Url\UrlConfig;
use Yiisoft\Yii\DataView\Url\UrlParametersFactory;

use function call_user_func_array;
use function count;
use function in_array;

/**
 * `GlobalContext` provides context for rendering and handling grid column headers, footers and container cells.
 *
 * @psalm-import-type UrlCreator from BaseListView
 */
final class GlobalContext
{
    /**
     * Creates a new context instance.
     *
     * @param Sort|null $originalSort Original sort configuration before any modifications.
     * @param Sort|null $sort Current sort configuration that reflects the active sort state.
     * @param string[] $allowedProperties List of properties that are allowed to be sorted.
     * @param string|null $sortableHeaderClass CSS class for sortable headers.
     * @param string|Stringable $sortableHeaderPrepend Content to prepend to sortable headers.
     * @param string|Stringable $sortableHeaderAppend Content to append to sortable headers.
     * @param string|null $sortableHeaderAscClass CSS class for ascending sort headers.
     * @param string|Stringable $sortableHeaderAscPrepend Content to prepend to ascending sort headers.
     * @param string|Stringable $sortableHeaderAscAppend Content to append to ascending sort headers.
     * @param string|null $sortableHeaderDescClass CSS class for descending sort headers.
     * @param string|Stringable $sortableHeaderDescPrepend Content to prepend to descending sort headers.
     * @param string|Stringable $sortableHeaderDescAppend Content to append to descending sort headers.
     * @param array $sortableLinkAttributes HTML attributes for sort links.
     * @param string|null $sortableLinkAscClass CSS class for ascending sort links.
     * @param string|null $sortableLinkDescClass CSS class for descending sort links.
     * @param PageToken|null $pageToken Current page token for pagination.
     * @param int|null $pageSize Number of items per page.
     * @param bool $multiSort Whether multiple column sorting is enabled.
     * @param UrlConfig $urlConfig URL configuration settings.
     * @param UrlCreator|null $urlCreator Callback for creating sort URLs.
     * @param TranslatorInterface $translator Translator service for header content.
     * @param string $translationCategory Category for header translations.
     *
     * @internal
     *
     * @psalm-param list<string> $allowedProperties
     * @psalm-param UrlCreator|null $urlCreator
     */
    public function __construct(
        private readonly ?Sort $originalSort,
        private readonly ?Sort $sort,
        private readonly array $allowedProperties,
        private readonly ?string $sortableHeaderClass,
        private readonly string|Stringable $sortableHeaderPrepend,
        private readonly string|Stringable $sortableHeaderAppend,
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
        private readonly ?int $pageSize,
        private readonly bool $multiSort,
        private readonly UrlConfig $urlConfig,
        private $urlCreator,
        private readonly TranslatorInterface $translator,
        private readonly string $translationCategory,
    ) {}

    /**
     * Translate a message using the grid's translation category.
     *
     * @param string|Stringable $id Message ID to translate.
     *
     * @return string Translated message.
     */
    public function translate(string|Stringable $id): string
    {
        return $this->translator->translate($id, category: $this->translationCategory);
    }

    /**
     * Prepare a sortable header cell with appropriate styling and links.
     *
     * @param Cell $cell The header cell to prepare.
     * @param string $property The property name for sorting.
     *
     * @return array Array containing:
     *   - Modified cell
     *   - Sort link (or null)
     *   - Content to prepend
     *   - Content to append
     *
     * @psalm-return list{Cell, ?A, string, string}
     */
    public function prepareSortable(Cell $cell, string $property): array
    {
        if (
            !in_array($property, $this->allowedProperties, true)
            || $this->sort === null
            || $this->originalSort === null
            || !$this->sort->hasFieldInConfig($property)
        ) {
            return [$cell, null, '', ''];
        }

        $linkAttributes = $this->sortableLinkAttributes;
        $propertyOrder = $this->sort->getOrder()[$property] ?? null;
        if ($propertyOrder === null) {
            $cell = $cell->addClass($this->sortableHeaderClass);
            $prepend = $this->sortableHeaderPrepend;
            $append = $this->sortableHeaderAppend;
        } else {
            $cell = $cell->addClass(
                $propertyOrder === 'asc' ? $this->sortableHeaderAscClass : $this->sortableHeaderDescClass,
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
            ),
        );

        return [
            $cell,
            A::tag()->attributes($linkAttributes)->url($url),
            (string) $prepend,
            (string) $append,
        ];
    }

    /**
     * Get the sort value for a link based on the current sort state.
     *
     * @param Sort $originalSort Original sort configuration.
     * @param Sort $sort Current sort configuration.
     * @param string $property Property name for sorting.
     *
     * @return string|null Sort value for the link or `null` if unchanged.
     */
    private function getLinkSortValue(
        Sort $originalSort,
        Sort $sort,
        string $property,
    ): ?string {
        $originalOrder = $originalSort->getOrder();
        $order = $sort->getOrder();

        if (isset($order[$property])) {
            if ($this->multiSort) {
                if ($order[$property] === 'asc') {
                    $order[$property] = 'desc';
                } elseif (!empty($originalOrder) && count($order) === 1) {
                    $order[$property] = 'asc';
                } else {
                    unset($order[$property]);
                }
            } elseif (isset($originalOrder[$property])) {
                if ($order[$property] === $originalOrder[$property]) {
                    $order = [$property => $originalOrder[$property] === 'asc' ? 'desc' : 'asc'];
                } else {
                    unset($order[$property]);
                }
            } elseif ($order[$property] === 'asc') {
                $order = [$property => 'desc'];
            } else {
                unset($order[$property]);
            }
        } elseif ($this->multiSort) {
            $order[$property] = 'asc';
        } else {
            $order = [$property => 'asc'];
        }

        if ($this->isEqualOrders($order, $originalOrder)) {
            return null;
        }

        $resultOrder = $sort->withOrder($order)->getOrder();
        if (empty($resultOrder)) {
            return null;
        }

        return OrderHelper::arrayToString($resultOrder);
    }

    /**
     * Compare two sort orders for equality.
     *
     * @param array $a First sort order.
     * @param array $b Second sort order.
     *
     * @return bool Whether the sort orders are equal.
     */
    private function isEqualOrders(array $a, array $b): bool
    {
        ksort($a);
        ksort($b);
        return $a === $b;
    }
}
