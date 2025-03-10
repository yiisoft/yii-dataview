<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;
use Yiisoft\Yii\DataView\Column\RadioColumn;
use Yiisoft\Yii\DataView\Column\RadioColumnRenderer;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\UrlConfig;

final class RadioColumnRendererTest extends TestCase
{
    private IterableDataReader $dataReader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataReader = new IterableDataReader([
            ['id' => 1, 'name' => 'John', 'age' => 20],
            ['id' => 2, 'name' => 'Mary', 'age' => 21],
        ]);
    }

    public function testRenderColumn(): void
    {
        $column = new RadioColumn(
            columnAttributes: ['class' => 'test-column']
        );
        $cell = new Cell();
        $translator = Mock::translator('en');

        $context = new GlobalContext(
            dataReader: $this->dataReader,
            pathArguments: [],
            queryParameters: [],
            translator: $translator,
            translationCategory: 'test'
        );

        $renderer = new RadioColumnRenderer();
        $result = $renderer->renderColumn($column, $cell, $context);

        $this->assertSame(['class' => 'test-column'], $result->getAttributes());
    }

    public function testRenderHeaderWithNullHeader(): void
    {
        $column = new RadioColumn();
        $cell = new Cell();
        $translator = Mock::translator('en');

        $context = new HeaderContext(
            originalSort: null,
            sort: null,
            orderProperties: [],
            sortableHeaderClass: '',
            sortableHeaderPrepend: '',
            sortableHeaderAppend: '',
            sortableHeaderAscClass: '',
            sortableHeaderAscPrepend: '',
            sortableHeaderAscAppend: '',
            sortableHeaderDescClass: '',
            sortableHeaderDescPrepend: '',
            sortableHeaderDescAppend: '',
            sortableLinkAttributes: [],
            sortableLinkAscClass: '',
            sortableLinkDescClass: '',
            pageToken: null,
            pageSize: 10,
            multiSort: false,
            urlConfig: new UrlConfig(),
            urlCreator: null,
            translator: $translator,
            translationCategory: 'test'
        );

        $renderer = new RadioColumnRenderer();
        $result = $renderer->renderHeader($column, $cell, $context);

        $this->assertNull($result);
    }

    public function testRenderHeaderWithCustomHeader(): void
    {
        $column = new RadioColumn(
            header: 'Select',
            headerAttributes: ['class' => 'header-class']
        );
        $cell = new Cell();
        $translator = Mock::translator('en');

        $context = new HeaderContext(
            originalSort: null,
            sort: null,
            orderProperties: [],
            sortableHeaderClass: '',
            sortableHeaderPrepend: '',
            sortableHeaderAppend: '',
            sortableHeaderAscClass: '',
            sortableHeaderAscPrepend: '',
            sortableHeaderAscAppend: '',
            sortableHeaderDescClass: '',
            sortableHeaderDescPrepend: '',
            sortableHeaderDescAppend: '',
            sortableLinkAttributes: [],
            sortableLinkAscClass: '',
            sortableLinkDescClass: '',
            pageToken: null,
            pageSize: 10,
            multiSort: false,
            urlConfig: new UrlConfig(),
            urlCreator: null,
            translator: $translator,
            translationCategory: 'test'
        );

        $renderer = new RadioColumnRenderer();
        $result = $renderer->renderHeader($column, $cell, $context);

        $this->assertNotNull($result);
        $this->assertSame('Select', $result->getContent()[0]);
        $this->assertArrayHasKey('class', $result->getAttributes());
        $this->assertSame('header-class', $result->getAttributes()['class']);
    }

    public function testRenderBodyWithDefaultSettings(): void
    {
        $column = new RadioColumn();
        $cell = new Cell();
        $data = ['id' => 1, 'name' => 'John', 'age' => 20];

        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 1,
            index: 0
        );

        $renderer = new RadioColumnRenderer();
        $result = $renderer->renderBody($column, $cell, $context);

        $content = $result->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('radio-selection', (string)$content[0]);
        $this->assertStringContainsString('value="1"', (string)$content[0]);
    }

    public function testRenderBodyWithCustomInputAttributes(): void
    {
        $column = new RadioColumn(
            inputAttributes: [
                'name' => 'custom-radio',
                'value' => 'custom-value',
                'class' => 'radio-class',
            ]
        );
        $cell = new Cell();
        $data = ['id' => 1, 'name' => 'John', 'age' => 20];

        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 1,
            index: 0
        );

        $renderer = new RadioColumnRenderer();
        $result = $renderer->renderBody($column, $cell, $context);

        $content = $result->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('name="custom-radio"', (string)$content[0]);
        $this->assertStringContainsString('value="custom-value"', (string)$content[0]);
        $this->assertStringContainsString('class="radio-class"', (string)$content[0]);
    }

    public function testRenderBodyWithCustomContent(): void
    {
        $column = new RadioColumn(
            content: static fn($input, $context) => Html::div($input)->class('custom-wrapper')
        );
        $cell = new Cell();
        $data = ['id' => 1, 'name' => 'John', 'age' => 20];

        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 1,
            index: 0
        );

        $renderer = new RadioColumnRenderer();
        $result = $renderer->renderBody($column, $cell, $context);

        $content = $result->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('<div class="custom-wrapper">', (string)$content[0]);
        $this->assertStringContainsString('radio-selection', (string)$content[0]);
    }

    public function testRenderFooterWithContent(): void
    {
        $column = new RadioColumn(
            footer: 'Footer content'
        );
        $cell = new Cell();
        $translator = Mock::translator('en');

        $context = new GlobalContext(
            dataReader: $this->dataReader,
            pathArguments: [],
            queryParameters: [],
            translator: $translator,
            translationCategory: 'test'
        );

        $renderer = new RadioColumnRenderer();
        $result = $renderer->renderFooter($column, $cell, $context);

        $this->assertSame('Footer content', $result->getContent()[0]);
    }

    public function testRenderFooterWithoutContent(): void
    {
        $column = new RadioColumn();
        $cell = new Cell();
        $translator = Mock::translator('en');

        $context = new GlobalContext(
            dataReader: $this->dataReader,
            pathArguments: [],
            queryParameters: [],
            translator: $translator,
            translationCategory: 'test'
        );

        $renderer = new RadioColumnRenderer();
        $result = $renderer->renderFooter($column, $cell, $context);

        $this->assertEmpty($result->getContent());
    }
}
