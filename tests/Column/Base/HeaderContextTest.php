<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column\Base;

use PHPUnit\Framework\TestCase;
use Stringable;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Html\Tag\A;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\UrlConfig;

/**
 * @covers \Yiisoft\Yii\DataView\Column\Base\HeaderContext
 * @covers \Yiisoft\Yii\DataView\Column\Base\Cell
 * @covers \Yiisoft\Yii\DataView\UrlConfig
 * @covers \Yiisoft\Yii\DataView\UrlParametersFactory
 */
final class HeaderContextTest extends TestCase
{
    public function testTranslate(): void
    {
        $translator = Mock::translator('en');

        $headerContext = $this->createHeaderContext(translator: $translator);

        $result = $headerContext->translate('test.message');

        $this->assertSame('test.message', $result);
    }

    public function testTranslateWithStringable(): void
    {
        $translator = Mock::translator('en');

        $headerContext = $this->createHeaderContext(translator: $translator);

        $stringable = new class () {
            public function __toString(): string
            {
                return 'Stringable Message';
            }
        };

        $result = $headerContext->translate($stringable);

        $this->assertSame('Stringable Message', $result);
    }

    public function testPrepareSortableWithEmptyProperty(): void
    {
        $cell = new Cell();
        $headerContext = $this->createHeaderContext();

        $result = $headerContext->prepareSortable($cell, 'nonexistent');

        $this->assertSame($cell, $result[0]);
        $this->assertNull($result[1]);
        $this->assertSame('', $result[2]);
        $this->assertSame('', $result[3]);
    }

    public function testPrepareSortableWithNullSort(): void
    {
        $cell = new Cell();
        $headerContext = $this->createHeaderContext(
            sort: null,
            originalSort: null
        );

        $result = $headerContext->prepareSortable($cell, 'name');

        $this->assertSame($cell, $result[0]);
        $this->assertNull($result[1]);
        $this->assertSame('', $result[2]);
        $this->assertSame('', $result[3]);
    }

    public function testPrepareSortableWithPropertyNotInConfig(): void
    {
        $sort = Sort::any();
        $cell = new Cell();
        $headerContext = $this->createHeaderContext(
            sort: $sort,
            originalSort: $sort,
            orderProperties: ['name' => 'name']
        );

        $result = $headerContext->prepareSortable($cell, 'age');

        $this->assertSame($cell, $result[0]);
        $this->assertNull($result[1]);
        $this->assertSame('', $result[2]);
        $this->assertSame('', $result[3]);
    }

    public function testPrepareSortableWithNoOrder(): void
    {
        $sort = Sort::any(['name' => []]);
        $cell = new Cell();
        $headerContext = $this->createHeaderContext(
            sort: $sort,
            originalSort: $sort,
            orderProperties: ['name' => 'name'],
            sortableHeaderClass: 'sortable',
            sortableHeaderPrepend: '↕',
            sortableHeaderAppend: '!'
        );

        $result = $headerContext->prepareSortable($cell, 'name');

        $this->assertInstanceOf(Cell::class, $result[0]);
        $this->assertStringContainsString('sortable', $result[0]->getAttributes()['class']);
        $this->assertInstanceOf(A::class, $result[1]);
        $this->assertSame('↕', $result[2]);
        $this->assertSame('!', $result[3]);
    }

    public function testPrepareSortableWithAscOrder(): void
    {
        $sort = Sort::any(['name' => []])->withOrder(['name' => 'asc']);
        $cell = new Cell();
        $headerContext = $this->createHeaderContext(
            sort: $sort,
            originalSort: $sort,
            orderProperties: ['name' => 'name'],
            sortableHeaderAscClass: 'asc',
            sortableHeaderAscPrepend: '↑',
            sortableHeaderAscAppend: '!',
            sortableLinkAscClass: 'link-asc'
        );

        $result = $headerContext->prepareSortable($cell, 'name');

        $this->assertInstanceOf(Cell::class, $result[0]);
        $this->assertStringContainsString('asc', $result[0]->getAttributes()['class']);
        $this->assertInstanceOf(A::class, $result[1]);
        $this->assertSame('↑', $result[2]);
        $this->assertSame('!', $result[3]);
    }

    public function testPrepareSortableWithDescOrder(): void
    {
        $sort = Sort::any(['name' => []])->withOrder(['name' => 'desc']);
        $cell = new Cell();
        $headerContext = $this->createHeaderContext(
            sort: $sort,
            originalSort: $sort,
            orderProperties: ['name' => 'name'],
            sortableHeaderDescClass: 'desc',
            sortableHeaderDescPrepend: '↓',
            sortableHeaderDescAppend: '!',
            sortableLinkDescClass: 'link-desc'
        );

        $result = $headerContext->prepareSortable($cell, 'name');

        $this->assertInstanceOf(Cell::class, $result[0]);
        $this->assertStringContainsString('desc', $result[0]->getAttributes()['class']);
        $this->assertInstanceOf(A::class, $result[1]);
        $this->assertSame('↓', $result[2]);
        $this->assertSame('!', $result[3]);
    }

    private function createHeaderContext(
        ?Sort $sort = null,
        ?Sort $originalSort = null,
        array $orderProperties = ['name' => 'name'],
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
    ): HeaderContext {
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

        return new HeaderContext(
            originalSort: $originalSort,
            sort: $sort,
            orderProperties: $orderProperties,
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
