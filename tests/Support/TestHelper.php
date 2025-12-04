<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use Stringable;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\BaseListView;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\GridView\Column\ColumnInterface;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\Url\UrlConfig;

final class TestHelper
{
    public static function createGlobalContext(
        ?Sort $sort = null,
        ?Sort $originalSort = null,
        array $allowedProperties = [],
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
        ?TranslatorInterface $translator = null,
        ?callable $urlCreator = null,
    ): GlobalContext {
        return new GlobalContext(
            originalSort: $originalSort ?? Sort::any(),
            sort: $sort ?? Sort::any(),
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
            urlConfig: new UrlConfig(
                pageParameterName: 'page',
                previousPageParameterName: 'prev',
                pageSizeParameterName: 'per-page',
                sortParameterName: 'sort',
            ),
            urlCreator: $urlCreator ?? new SimplePaginationUrlCreator(),
            translator: $translator ?? new Translator('en'),
            translationCategory: BaseListView::DEFAULT_TRANSLATION_CATEGORY,
        );
    }

    public static function createDataContext(
        ?ReadableDataInterface $preparedDataReader = null,
        ColumnInterface $column = new DataColumn('id'),
        array|object $data = [],
        int|string $key = 0,
        int $index = 0,
    ): DataContext {
        return new DataContext($preparedDataReader, $column, $data, $key, $index);
    }
}
