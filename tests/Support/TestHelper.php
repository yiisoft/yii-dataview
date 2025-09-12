<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use Stringable;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\UrlConfig;

final class TestHelper
{
    public static function createGlobalContext(
        ?Sort $sort = null,
        ?Sort $originalSort = null,
        array $allowedProperties = ['name'],
        ?string $sortableHeaderClass = null,
        string|Stringable $sortableHeaderPrepend = '',
        string|Stringable $sortableHeaderAppend = '',
        ?string $sortableHeaderAscClass = null,
        string|Stringable $sortableHeaderAscPrepend = '',
        string|Stringable $sortableHeaderAscAppend = '',
        ?string $sortableHeaderDescClass = null,
        string|Stringable $sortableHeaderDescPrepend = '',
        string|Stringable $sortableHeaderDescAppend = '',
        array $sortableLinkAttributes = [],
        ?string $sortableLinkAscClass = null,
        ?string $sortableLinkDescClass = null,
        ?PageToken $pageToken = null,
        ?int $pageSize = null,
        bool $multiSort = false,
        ?TranslatorInterface $translator = null
    ): GlobalContext {
        if ($sort === null) {
            $sort = Sort::any();
        }

        if ($originalSort === null) {
            $originalSort = Sort::any();
        }

        if ($translator === null) {
            $translator = Mock::translator('en');
        }

        $urlConfig = new UrlConfig(
            pageParameterName: 'page',
            previousPageParameterName: 'prev',
            pageSizeParameterName: 'per-page',
            sortParameterName: 'sort'
        );

        $urlCreator = fn(): string => '#';

        return new GlobalContext(
            originalSort: $originalSort,
            sort: $sort,
            allowedProperties: $allowedProperties,
            sortableHeaderClass: $sortableHeaderClass,
            sortableHeaderPrepend: $sortableHeaderPrepend,
            sortableHeaderAppend: $sortableHeaderAppend,
            sortableHeaderAscClass: $sortableHeaderAscClass,
            sortableHeaderAscPrepend: $sortableHeaderAscPrepend,
            sortableHeaderAscAppend: $sortableHeaderAscAppend,
            sortableHeaderDescClass: $sortableHeaderDescClass,
            sortableHeaderDescPrepend: $sortableHeaderDescPrepend,
            sortableHeaderDescAppend: $sortableHeaderDescAppend,
            sortableLinkAttributes: $sortableLinkAttributes,
            sortableLinkAscClass: $sortableLinkAscClass,
            sortableLinkDescClass: $sortableLinkDescClass,
            pageToken: $pageToken,
            pageSize: $pageSize,
            multiSort: $multiSort,
            urlConfig: $urlConfig,
            urlCreator: $urlCreator,
            translator: $translator,
            translationCategory: 'grid'
        );
    }
}
