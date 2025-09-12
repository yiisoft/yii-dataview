<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column\Base;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Html\Tag\A;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestHelper;

/**
 * @covers \Yiisoft\Yii\DataView\Column\Base\GlobalContext
 * @covers \Yiisoft\Yii\DataView\Column\Base\Cell
 * @covers \Yiisoft\Yii\DataView\UrlConfig
 * @covers \Yiisoft\Yii\DataView\UrlParametersFactory
 */
final class GlobalContextTest extends TestCase
{
    public function testTranslate(): void
    {
        $translator = Mock::translator('en');

        $headerContext = TestHelper::createGlobalContext(translator: $translator);

        $result = $headerContext->translate('test.message');

        $this->assertSame('test.message', $result);
    }

    public function testTranslateWithStringable(): void
    {
        $translator = Mock::translator('en');

        $headerContext = TestHelper::createGlobalContext(translator: $translator);

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
        $headerContext = TestHelper::createGlobalContext();

        $result = $headerContext->prepareSortable($cell, 'nonexistent');

        $this->assertSame($cell, $result[0]);
        $this->assertNull($result[1]);
        $this->assertSame('', $result[2]);
        $this->assertSame('', $result[3]);
    }

    public function testPrepareSortableWithNullSort(): void
    {
        $cell = new Cell();
        $headerContext = TestHelper::createGlobalContext(
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
        $headerContext = TestHelper::createGlobalContext(
            sort: $sort,
            originalSort: $sort,
            allowedProperties: ['name']
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
        $headerContext = TestHelper::createGlobalContext(
            sort: $sort,
            originalSort: $sort,
            allowedProperties: ['name'],
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
        $headerContext = TestHelper::createGlobalContext(
            sort: $sort,
            originalSort: $sort,
            allowedProperties: ['name'],
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

        $headerContext = TestHelper::createGlobalContext(
            sort: $sort,
            originalSort: $sort,
            allowedProperties: ['name'],
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
}
