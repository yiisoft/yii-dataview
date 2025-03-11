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

    public function testRenderHeaderWithCustomAttributes(): void
    {
        $column = new RadioColumn(
            header: 'Select',
            headerAttributes: ['class' => 'header-class', 'data-test' => 'value']
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
        $this->assertArrayHasKey('data-test', $result->getAttributes());
        $this->assertSame('value', $result->getAttributes()['data-test']);
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

    public function testRenderBodyWithCustomContentAndAttributes(): void
    {
        $column = new RadioColumn(
            bodyAttributes: ['class' => 'body-class', 'data-index' => '1'],
            inputAttributes: ['class' => 'input-class', 'data-test' => 'value'],
            content: static fn($input) => Html::div($input)->class('custom-wrapper')
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
        $this->assertStringContainsString('class="input-class"', (string)$content[0]);
        $this->assertStringContainsString('data-test="value"', (string)$content[0]);
        $this->assertArrayHasKey('class', $result->getAttributes());
        $this->assertSame('body-class', $result->getAttributes()['class']);
        $this->assertArrayHasKey('data-index', $result->getAttributes());
        $this->assertSame('1', $result->getAttributes()['data-index']);
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

    public function testRenderBodyWithCustomContentAndEmptyAttributes(): void
    {
        $column = new RadioColumn(
            bodyAttributes: [],
            inputAttributes: [],
            content: static fn($input) => Html::div($input)->class('custom-wrapper')
        );
        $cell = new Cell();
        $data = ['id' => 1];

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
        $this->assertEmpty($result->getAttributes());
    }

    public function testRenderBodyWithCustomName(): void
    {
        $column = new RadioColumn(
            inputAttributes: ['name' => 'custom-selection']
        );
        $cell = new Cell();
        $data = ['id' => 1];

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
        $this->assertStringContainsString('name="custom-selection"', (string)$content[0]);
    }

    public function testRenderBodyWithInvisibleColumn(): void
    {
        $column = new RadioColumn(visible: false);
        $cell = new Cell();
        $data = ['id' => 1];

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
        $this->assertStringContainsString('type="radio"', (string)$content[0]);
    }

    public function testRenderColumnWithInvisibleColumn(): void
    {
        $column = new RadioColumn(
            columnAttributes: ['class' => 'test-column'],
            visible: false
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

    public function testRenderBodyWithDefaultNameAndValue(): void
    {
        $column = new RadioColumn();
        $cell = new Cell();
        $data = ['id' => 1];

        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 'test-key',
            index: 0
        );

        $renderer = new RadioColumnRenderer();
        $result = $renderer->renderBody($column, $cell, $context);

        $content = $result->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('name="radio-selection"', (string)$content[0]);
        $this->assertStringContainsString('value="test-key"', (string)$content[0]);
    }

    public function testRenderBodyWithCustomValue(): void
    {
        $column = new RadioColumn(
            inputAttributes: ['value' => 'custom-value']
        );
        $cell = new Cell();
        $data = ['id' => 1];

        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 'test-key',
            index: 0
        );

        $renderer = new RadioColumnRenderer();
        $result = $renderer->renderBody($column, $cell, $context);

        $content = $result->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('value="custom-value"', (string)$content[0]);
    }

    public function testRenderHeaderWithEmptyAttributes(): void
    {
        $column = new RadioColumn(
            header: 'Test Header',
            headerAttributes: []
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
        $this->assertSame('Test Header', $result->getContent()[0]);
        $this->assertEmpty($result->getAttributes());
    }

    public function testRenderColumnWithMultipleAttributes(): void
    {
        $column = new RadioColumn(
            columnAttributes: [
                'class' => 'test-column',
                'data-role' => 'radio-column',
                'data-index' => '1',
            ]
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

        $this->assertSame(
            [
                'class' => 'test-column',
                'data-role' => 'radio-column',
                'data-index' => '1',
            ],
            $result->getAttributes()
        );
    }

    public function testRenderHeaderWithHtmlSpecialChars(): void
    {
        $column = new RadioColumn(
            header: '<script>alert("xss")</script>',
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
        $this->assertSame('<script>alert("xss")</script>', $result->getContent()[0]);
        $this->assertSame(['class' => 'header-class'], $result->getAttributes());
    }

    public function testRenderFooterWithColumnAttributes(): void
    {
        $column = new RadioColumn(
            footer: 'Total',
            columnAttributes: ['class' => 'column-class']
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

        $this->assertSame('Total', $result->getContent()[0]);
    }

    public function testRenderBodyWithRealWorldData(): void
    {
        $column = new RadioColumn(
            bodyAttributes: ['class' => 'selection-column'],
            inputAttributes: [
                'name' => 'user-selection',
                'class' => 'user-radio',
                'form' => 'user-form',
            ]
        );
        $cell = new Cell();
        $data = [
            'id' => 42,
            'username' => 'john.doe',
            'email' => 'john@example.com',
        ];

        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 42,
            index: 0
        );

        $renderer = new RadioColumnRenderer();
        $result = $renderer->renderBody($column, $cell, $context);

        $content = $result->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('name="user-selection"', (string)$content[0]);
        $this->assertStringContainsString('class="user-radio"', (string)$content[0]);
        $this->assertStringContainsString('form="user-form"', (string)$content[0]);
        $this->assertStringContainsString('value="42"', (string)$content[0]);
        $this->assertSame(['class' => 'selection-column'], $result->getAttributes());
    }

    public function testRenderFooterWithHtmlContent(): void
    {
        $column = new RadioColumn(
            footer: '<strong>Total:</strong> 42'
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

        $this->assertSame('<strong>Total:</strong> 42', $result->getContent()[0]);
    }

    public function testRenderHeaderWithAttributesButNoContent(): void
    {
        $column = new RadioColumn(
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

        $this->assertNull($result);
    }

    public function testRenderBodyWithCustomHtmlContent(): void
    {
        $column = new RadioColumn(
            content: static fn($input) => Html::div()
                ->class('input-group')
                ->content(
                    Html::label()
                        ->content(
                            $input,
                            Html::span('*')->class('required'),
                            'Select user'
                        )
                )
        );
        $cell = new Cell();
        $data = ['id' => 1];

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
        $this->assertStringContainsString('<div class="input-group">', (string)$content[0]);
        $this->assertStringContainsString('Select user', (string)$content[0]);
        $this->assertStringContainsString('<span class="required">*</span>', (string)$content[0]);
        $this->assertFalse($result->shouldEncode());
    }

    public function testRenderBodyWithCustomLabelAndAttributes(): void
    {
        $column = new RadioColumn(
            bodyAttributes: ['class' => 'radio-cell'],
            inputAttributes: [
                'class' => 'form-radio',
                'data-testid' => 'radio-input',
            ],
            content: static fn($input) => Html::label($input, 'Select item')
                ->class('form-label')
                ->attribute('data-testid', 'radio-label')
        );
        $cell = new Cell();
        $data = ['id' => 1];

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
        $this->assertStringContainsString('class="form-label"', (string)$content[0]);
        $this->assertStringContainsString('data-testid="radio-label"', (string)$content[0]);
        $this->assertStringContainsString('class="form-radio"', (string)$content[0]);
        $this->assertStringContainsString('data-testid="radio-input"', (string)$content[0]);
        $this->assertSame(['class' => 'radio-cell'], $result->getAttributes());
    }

    public function testRenderBodyWithCheckedInput(): void
    {
        $column = new RadioColumn(
            inputAttributes: ['checked' => true]
        );
        $cell = new Cell();
        $data = ['id' => 1];

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
        $this->assertStringContainsString(' checked>', (string)$content[0]);
    }

    public function testRenderBodyWithDisabledInput(): void
    {
        $column = new RadioColumn(
            inputAttributes: ['disabled' => true]
        );
        $cell = new Cell();
        $data = ['id' => 1];

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
        $this->assertStringContainsString(' disabled>', (string)$content[0]);
    }

    public function testRenderBodyWithCustomInputName(): void
    {
        $column = new RadioColumn(
            name: 'selection'
        );
        $cell = new Cell();
        $data = ['id' => 42, 'name' => 'test'];

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
        $this->assertStringContainsString('name="selection"', (string)$content[0]);
    }

    public function testRenderBodyWithCustomContent(): void
    {
        $column = new RadioColumn(
            content: static fn($input) => $input . ' Custom label'
        );
        $cell = new Cell();
        $data = ['id' => 42];

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
        $this->assertStringContainsString('Custom label', (string)$content[0]);
    }

    public function testDefaultNameIsUsedWhenNotProvided(): void
    {
        // Arrange
        $column = new RadioColumn();
        $cell = new Cell();
        $data = ['id' => 42];
        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 1,
            index: 0
        );
        $renderer = new RadioColumnRenderer();

        // Act
        $result = $renderer->renderBody($column, $cell, $context);

        // Assert
        $content = $result->getContent();
        $this->assertStringContainsString('name="radio-selection"', (string)$content[0]);
    }

    public function testRenderBodyWithMultipleAttributes(): void
    {
        $column = new RadioColumn(
            bodyAttributes: ['class' => 'form-check'],
            inputAttributes: [
                'class' => 'form-check-input',
                'data-testid' => 'radio-input',
                'required' => true,
            ],
            name: 'selection'
        );
        $cell = new Cell();
        $data = ['id' => 42];

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
        $this->assertStringContainsString('class="form-check-input"', (string)$content[0]);
        $this->assertStringContainsString('data-testid="radio-input"', (string)$content[0]);
        $this->assertStringContainsString(' required>', (string)$content[0]);
        $this->assertSame(['class' => 'form-check'], $result->getAttributes());
    }

    public function testRenderBodyWithEmptyContentClosure(): void
    {
        $column = new RadioColumn(
            content: static fn() => ''
        );
        $cell = new Cell();
        $data = ['id' => 42];

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
        $this->assertSame('', (string)$content[0]);
        $this->assertFalse($result->shouldEncode());
    }

    public function testRenderBodyWithContextInContentClosure(): void
    {
        $column = new RadioColumn(
            content: static fn($input, $context) => (string)$input . ' Item ' . $context->index
        );
        $cell = new Cell();
        $data = ['id' => 42];

        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 1,
            index: 5
        );

        $renderer = new RadioColumnRenderer();
        $result = $renderer->renderBody($column, $cell, $context);

        $content = $result->getContent();
        $this->assertStringContainsString('Item 5', (string)$content[0]);
        $this->assertFalse($result->shouldEncode());
    }

    public function testRenderBodyWithAriaLabelAttribute(): void
    {
        $column = new RadioColumn(
            inputAttributes: ['aria-label' => 'Select item'],
            content: static fn($input) => (string)$input
        );
        $cell = new Cell();
        $data = ['id' => 42];

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
        $this->assertStringContainsString('aria-label="Select item"', (string)$content[0]);
        $this->assertFalse($result->shouldEncode());
    }

    public function testRenderBodyWithComplexContent(): void
    {
        $column = new RadioColumn(
            content: static fn($input) => Html::div()
                ->class('form-check')
                ->content(
                    $input,
                    Html::div()
                        ->class('help-text')
                        ->content('Select one option')
                )
        );
        $cell = new Cell();
        $data = ['id' => 42];

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
        $this->assertStringContainsString('class="form-check"', (string)$content[0]);
        $this->assertStringContainsString('class="help-text"', (string)$content[0]);
        $this->assertStringContainsString('Select one option', (string)$content[0]);
        $this->assertFalse($result->shouldEncode());
    }

    public function testRenderBodyWithBootstrapFormCheck(): void
    {
        $column = new RadioColumn(
            inputAttributes: ['class' => 'form-check-input'],
            content: static fn($input) => Html::div()
                ->class('form-check')
                ->content(
                    $input,
                    Html::label()
                        ->class('form-check-label')
                        ->content('Option 1')
                )
        );
        $cell = new Cell();
        $data = ['id' => 42];

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
        $this->assertStringContainsString('class="form-check"', (string)$content[0]);
        $this->assertStringContainsString('class="form-check-input"', (string)$content[0]);
        $this->assertStringContainsString('class="form-check-label"', (string)$content[0]);
        $this->assertStringContainsString('Option 1', (string)$content[0]);
        $this->assertFalse($result->shouldEncode());
    }

    public function testRenderBodyWithInlineFormCheck(): void
    {
        $column = new RadioColumn(
            inputAttributes: ['class' => 'form-check-input'],
            content: static fn($input) => Html::div()
                ->class('form-check form-check-inline')
                ->content(
                    $input,
                    Html::label()
                        ->class('form-check-label')
                        ->content('Option 1')
                )
        );
        $cell = new Cell();
        $data = ['id' => 42];

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
        $this->assertStringContainsString('class="form-check form-check-inline"', (string)$content[0]);
        $this->assertStringContainsString('class="form-check-input"', (string)$content[0]);
        $this->assertStringContainsString('class="form-check-label"', (string)$content[0]);
        $this->assertStringContainsString('Option 1', (string)$content[0]);
        $this->assertFalse($result->shouldEncode());
    }

    public function testRenderBodyWithDataAttributes(): void
    {
        $column = new RadioColumn(
            inputAttributes: [
                'class' => 'form-check-input',
                'data-bs-target' => '#target',
                'data-bs-action' => 'select',
            ],
            content: static fn($input) => Html::div()
                ->class('form-check')
                ->attribute('data-bs-toggle', 'tooltip')
                ->attribute('title', 'Select this option')
                ->content($input)
        );
        $cell = new Cell();
        $data = ['id' => 42];

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
        $this->assertStringContainsString('data-bs-toggle="tooltip"', (string)$content[0]);
        $this->assertStringContainsString('title="Select this option"', (string)$content[0]);
        $this->assertStringContainsString('data-bs-target="#target"', (string)$content[0]);
        $this->assertStringContainsString('data-bs-action="select"', (string)$content[0]);
        $this->assertFalse($result->shouldEncode());
    }

    public function testRenderBodyWithCustomValueAndChecked(): void
    {
        $column = new RadioColumn(
            inputAttributes: [
                'value' => '42_custom',
                'checked' => true,
            ],
            content: static fn($input) => Html::div()
                ->class('form-check')
                ->content($input)
        );
        $cell = new Cell();
        $data = ['id' => 42];

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
        $this->assertStringContainsString('value="42_custom"', (string)$content[0]);
        $this->assertStringContainsString('checked', (string)$content[0]);
        $this->assertFalse($result->shouldEncode());
    }

    public function testEmptyNameAttributeIsPreserved(): void
    {
        // Arrange
        $column = new RadioColumn(
            inputAttributes: [
                'name' => '',
            ],
            content: static fn($input) => Html::div()
                ->class('form-check')
                ->content($input)
        );
        $cell = new Cell();
        $data = ['id' => 42];
        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 1,
            index: 0
        );
        $renderer = new RadioColumnRenderer();

        // Act
        $result = $renderer->renderBody($column, $cell, $context);

        // Assert
        $content = $result->getContent();
        $this->assertStringContainsString('name value="1"', (string)$content[0]);
    }

    public function testEmptyValueAttributeIsPreserved(): void
    {
        // Arrange
        $column = new RadioColumn(
            inputAttributes: [
                'value' => '',
            ],
            content: static fn($input) => Html::div()
                ->class('form-check')
                ->content($input)
        );
        $cell = new Cell();
        $data = ['id' => 42];
        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 1,
            index: 0
        );
        $renderer = new RadioColumnRenderer();

        // Act
        $result = $renderer->renderBody($column, $cell, $context);

        // Assert
        $content = $result->getContent();
        $this->assertStringContainsString('name="radio-selection" value', (string)$content[0]);
    }

    public function testRenderBodyWithActiveStatus(): void
    {
        $column = new RadioColumn(
            inputAttributes: [
                'class' => 'form-check-input',
                'value' => 'active',
                'checked' => true,
            ],
            name: 'status',
            content: static fn($input, $context) => Html::div()
                ->class('form-check')
                ->content(
                    $input,
                    Html::label()
                        ->class('form-check-label')
                        ->content($context->data['status'])
                )
        );

        $cell = new Cell();
        $data = ['id' => 42, 'status' => 'active'];

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
        $this->assertStringContainsString('name="status"', (string)$content[0]);
        $this->assertStringContainsString('value="active"', (string)$content[0]);
        $this->assertStringContainsString('checked', (string)$content[0]);
        $this->assertFalse($result->shouldEncode());
    }

    public function testRenderBodyWithInactiveStatus(): void
    {
        $column = new RadioColumn(
            inputAttributes: [
                'class' => 'form-check-input',
                'value' => 'inactive',
            ],
            name: 'status',
            content: static fn($input, $context) => Html::div()
                ->class('form-check')
                ->content(
                    $input,
                    Html::label()
                        ->class('form-check-label')
                        ->content($context->data['status'])
                )
        );

        $cell = new Cell();
        $data = ['id' => 43, 'status' => 'inactive'];

        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 2,
            index: 1
        );

        $renderer = new RadioColumnRenderer();
        $result = $renderer->renderBody($column, $cell, $context);

        $content = $result->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('name="status"', (string)$content[0]);
        $this->assertStringContainsString('value="inactive"', (string)$content[0]);
        $this->assertStringNotContainsString('checked', (string)$content[0]);
        $this->assertFalse($result->shouldEncode());
    }
}
