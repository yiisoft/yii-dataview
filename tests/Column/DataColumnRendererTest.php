<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use DateTime;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Validator;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\FilterContext;
use Yiisoft\Yii\DataView\Column\Base\MakeFilterContext;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\Column\DataColumnRenderer;
use Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestHelper;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\UrlConfig;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Yii\DataView\UrlParameterProviderInterface;

final class DataColumnRendererTest extends TestCase
{
    use TestTrait;

    private ContainerInterface $filterFactoryContainer;
    private IterableDataReader $dataReader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filterFactoryContainer = new Container(ContainerConfig::create());

        $this->dataReader = new IterableDataReader([
            ['id' => 1, 'name' => 'John', 'age' => 20],
            ['id' => 2, 'name' => 'Mary', 'age' => 21],
        ]);
    }

    public function testRenderColumn(): void
    {
        $this->expectNotToPerformAssertions();

        $column = new DataColumn('test');
        $cell = new Cell();

        $context = TestHelper::createGlobalContext();

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $renderer->renderColumn($column, $cell, $context);
    }

    public function testRenderHeader(): void
    {
        $column = new DataColumn('test', 'Test Header');
        $cell = new Cell();
        $translator = Mock::translator('en');

        $sort = Sort::any();

        $context = new GlobalContext(
            originalSort: $sort,
            sort: $sort,
            orderProperties: ['test' => 'test'],
            sortableHeaderClass: 'sortable',
            sortableHeaderPrepend: '',
            sortableHeaderAppend: '',
            sortableHeaderAscClass: 'asc',
            sortableHeaderAscPrepend: '',
            sortableHeaderAscAppend: '',
            sortableHeaderDescClass: 'desc',
            sortableHeaderDescPrepend: '',
            sortableHeaderDescAppend: '',
            sortableLinkAttributes: [],
            sortableLinkAscClass: 'asc-link',
            sortableLinkDescClass: 'desc-link',
            pageToken: null,
            pageSize: 10,
            multiSort: false,
            urlConfig: new UrlConfig(),
            urlCreator: null,
            translator: $translator,
            translationCategory: 'test'
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderHeader($column, $cell, $context);

        $this->assertNotEmpty($result->getContent());
    }

    public function testRenderBody(): void
    {
        $column = new DataColumn('name');
        $cell = new Cell();
        $data = ['id' => 1, 'name' => 'John Doe', 'age' => 20];

        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 1,
            index: 0
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderBody($column, $cell, $context);

        $content = $result->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('John Doe', (string)$content[0]);
    }

    public function testGetOrderProperties(): void
    {
        $column = new DataColumn('test');
        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->getOrderProperties($column);
        $this->assertEquals(['test' => 'test'], $result);
    }

    public function testRenderBodyWithCustomContentCallback(): void
    {
        $column = new DataColumn(
            'name',
            content: static fn (array $data) => strtoupper($data['name'])
        );
        $cell = new Cell();
        $data = ['id' => 1, 'name' => 'John Doe', 'age' => 20];

        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 1,
            index: 0
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderBody($column, $cell, $context);
        $content = $result->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('JOHN DOE', (string)$content[0]);
    }

    public function testRenderBodyWithDateTime(): void
    {
        $date = new DateTime('2025-03-06 02:00:22');
        $column = new DataColumn('created_at', dateTimeFormat: 'Y-m-d');
        $cell = new Cell();
        $data = ['id' => 1, 'created_at' => $date];

        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 1,
            index: 0
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderBody($column, $cell, $context);
        $content = $result->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('2025-03-06', (string)$content[0]);
    }

    public function testRenderBodyWithDynamicAttributes(): void
    {
        $column = new DataColumn(
            'age',
            bodyAttributes: static fn (array $data) => ['class' => $data['age'] >= 21 ? 'adult' : 'minor']
        );
        $cell = new Cell();
        $data = ['id' => 2, 'name' => 'Mary', 'age' => 21];

        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 1,
            index: 0
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderBody($column, $cell, $context);
        $this->assertStringContainsString('adult', $result->getAttributes()['class']);
    }

    public function testRenderFilterWithDropdown(): void
    {
        $column = new DataColumn(
            'status',
            filter: ['active' => 'Active', 'inactive' => 'Inactive']
        );
        $cell = new Cell();

        $urlParameterProvider = new class () implements UrlParameterProviderInterface {
            public function get(string $name, int $type): ?string
            {
                return 'active';
            }
        };

        $context = new FilterContext(
            'filter-form',
            new Result(),
            'invalid',
            ['class' => 'error-container'],
            $urlParameterProvider
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderFilter($column, $cell, $context);
        $this->assertNotNull($result);
        $content = $result->getContent();
        $this->assertStringContainsString('select', (string)$content[0]);
        $this->assertStringContainsString('Active', (string)$content[0]);
        $this->assertStringContainsString('Inactive', (string)$content[0]);
    }

    public function testMakeFilterWithCustomFactory(): void
    {
        $column = new DataColumn(
            'name',
            filterFactory: LikeFilterFactory::class
        );

        $urlParameterProvider = new class () implements UrlParameterProviderInterface {
            public function get(string $name, int $type): ?string
            {
                return match ($name) {
                    'name' => 'John',
                    'age' => 'not-a-number',
                    'status' => '',
                    default => null,
                };
            }
        };

        $context = new MakeFilterContext(
            new Result(),
            $urlParameterProvider
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->makeFilter($column, $context);
        $this->assertNotNull($result);
    }

    public function testMakeFilterWithValidation(): void
    {
        $column = new DataColumn(
            'age',
            filterValidation: [new Number()]
        );

        $urlParameterProvider = new class () implements UrlParameterProviderInterface {
            public function get(string $name, int $type): ?string
            {
                return match ($name) {
                    'name' => 'John',
                    'age' => 'not-a-number',
                    'status' => '',
                    default => null,
                };
            }
        };

        $context = new MakeFilterContext(
            new Result(),
            $urlParameterProvider
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->makeFilter($column, $context);
        $this->assertNull($result);
        $this->assertNotEmpty($context->validationResult->getErrors());
    }

    public function testMakeFilterWithEmptyValue(): void
    {
        $column = new DataColumn(
            'status',
            filterEmpty: true
        );

        $urlParameterProvider = new class () implements UrlParameterProviderInterface {
            public function get(string $name, int $type): ?string
            {
                return match ($name) {
                    'name' => 'John',
                    'age' => 'not-a-number',
                    'status' => '',
                    default => null,
                };
            }
        };

        $context = new MakeFilterContext(
            new Result(),
            $urlParameterProvider
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->makeFilter($column, $context);
        $this->assertNull($result);
    }

    public function testRenderHeaderWithoutProperty(): void
    {
        $column = new DataColumn(header: 'Custom Header');
        $cell = new Cell();
        $translator = Mock::translator('en');
        $sort = Sort::any();

        $context = new GlobalContext(
            originalSort: $sort,
            sort: $sort,
            orderProperties: [],
            sortableHeaderClass: 'sortable',
            sortableHeaderPrepend: '',
            sortableHeaderAppend: '',
            sortableHeaderAscClass: 'asc',
            sortableHeaderAscPrepend: '',
            sortableHeaderAscAppend: '',
            sortableHeaderDescClass: 'desc',
            sortableHeaderDescPrepend: '',
            sortableHeaderDescAppend: '',
            sortableLinkAttributes: [],
            sortableLinkAscClass: 'asc-link',
            sortableLinkDescClass: 'desc-link',
            pageToken: null,
            pageSize: 10,
            multiSort: false,
            urlConfig: new UrlConfig(),
            urlCreator: null,
            translator: $translator,
            translationCategory: 'test'
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderHeader($column, $cell, $context);
        $this->assertEquals('Custom Header', $result->getContent()[0]);
    }

    public function testRenderHeaderWithoutHeaderAndProperty(): void
    {
        $column = new DataColumn();
        $cell = new Cell();
        $translator = Mock::translator('en');
        $sort = Sort::any();

        $context = new GlobalContext(
            originalSort: $sort,
            sort: $sort,
            orderProperties: [],
            sortableHeaderClass: 'sortable',
            sortableHeaderPrepend: '',
            sortableHeaderAppend: '',
            sortableHeaderAscClass: 'asc',
            sortableHeaderAscPrepend: '',
            sortableHeaderAscAppend: '',
            sortableHeaderDescClass: 'desc',
            sortableHeaderDescPrepend: '',
            sortableHeaderDescAppend: '',
            sortableLinkAttributes: [],
            sortableLinkAscClass: 'asc-link',
            sortableLinkDescClass: 'desc-link',
            pageToken: null,
            pageSize: 10,
            multiSort: false,
            urlConfig: new UrlConfig(),
            urlCreator: null,
            translator: $translator,
            translationCategory: 'test'
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderHeader($column, $cell, $context);
        $this->assertEquals('', $result->getContent()[0]);
    }

    public function testRenderBodyWithNullContent(): void
    {
        $column = new DataColumn();
        $cell = new Cell();
        $data = ['id' => 1, 'name' => null];

        $context = new DataContext(
            preparedDataReader: $this->dataReader,
            column: $column,
            data: $data,
            key: 1,
            index: 0
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderBody($column, $cell, $context);
        $this->assertEquals('', $result->getContent()[0]);
    }

    public function testRenderFooterWithContent(): void
    {
        $column = new DataColumn(footer: 'Total: 100');
        $cell = new Cell();

        $context = TestHelper::createGlobalContext();

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderFooter($column, $cell, $context);
        $this->assertEquals('Total: 100', $result->getContent()[0]);
    }

    public function testRenderFooterWithoutContent(): void
    {
        $column = new DataColumn();
        $cell = new Cell();

        $context = TestHelper::createGlobalContext();

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderFooter($column, $cell, $context);
        $this->assertEmpty($result->getContent());
    }

    public function testRenderFilterWithoutProperty(): void
    {
        $column = new DataColumn();
        $cell = new Cell();

        $urlParameterProvider = new class () implements UrlParameterProviderInterface {
            public function get(string $name, int $type): ?string
            {
                return null;
            }
        };

        $context = new FilterContext(
            'filter-form',
            new Result(),
            'invalid',
            ['class' => 'error-container'],
            $urlParameterProvider
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderFilter($column, $cell, $context);
        $this->assertNull($result);
    }

    public function testRenderFilterWithFilterDisabled(): void
    {
        $column = new DataColumn('name', filter: false);
        $cell = new Cell();

        $urlParameterProvider = new class () implements UrlParameterProviderInterface {
            public function get(string $name, int $type): ?string
            {
                return null;
            }
        };

        $context = new FilterContext(
            'filter-form',
            new Result(),
            'invalid',
            ['class' => 'error-container'],
            $urlParameterProvider
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderFilter($column, $cell, $context);
        $this->assertNull($result);
    }

    public function testMakeFilterWithNeverEmptyCallback(): void
    {
        $column = new DataColumn(
            'status',
            filterEmpty: false
        );

        $urlParameterProvider = new class () implements UrlParameterProviderInterface {
            public function get(string $name, int $type): ?string
            {
                return 'test';
            }
        };

        $context = new MakeFilterContext(
            new Result(),
            $urlParameterProvider
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->makeFilter($column, $context);
        $this->assertNotNull($result);
    }

    public function testMakeFilterWithCustomEmptyCallback(): void
    {
        $column = new DataColumn(
            'status',
            filterEmpty: static fn (mixed $value): bool => $value === 'empty'
        );

        $urlParameterProvider = new class () implements UrlParameterProviderInterface {
            public function get(string $name, int $type): ?string
            {
                return 'empty';
            }
        };

        $context = new MakeFilterContext(
            new Result(),
            $urlParameterProvider
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->makeFilter($column, $context);
        $this->assertNull($result);
    }

    public function testRenderHeaderWithSortingDisabled(): void
    {
        $column = new DataColumn('test', withSorting: false);
        $cell = new Cell();
        $translator = Mock::translator('en');
        $sort = Sort::any();

        $context = new GlobalContext(
            originalSort: $sort,
            sort: $sort,
            orderProperties: ['test' => 'test'],
            sortableHeaderClass: 'sortable',
            sortableHeaderPrepend: '↑',
            sortableHeaderAppend: '↓',
            sortableHeaderAscClass: 'asc',
            sortableHeaderAscPrepend: '',
            sortableHeaderAscAppend: '',
            sortableHeaderDescClass: 'desc',
            sortableHeaderDescPrepend: '',
            sortableHeaderDescAppend: '',
            sortableLinkAttributes: [],
            sortableLinkAscClass: 'asc-link',
            sortableLinkDescClass: 'desc-link',
            pageToken: null,
            pageSize: 10,
            multiSort: false,
            urlConfig: new UrlConfig(),
            urlCreator: null,
            translator: $translator,
            translationCategory: 'test'
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderHeader($column, $cell, $context);
        $content = $result->getContent()[0];
        $this->assertEquals('Test', $content);
        $this->assertArrayNotHasKey('class', $result->getAttributes());
    }

    public function testRenderHeaderWithSorting(): void
    {
        $column = new DataColumn('test', withSorting: true);
        $cell = new Cell();
        $translator = Mock::translator('en');
        $sort = Sort::only(['test']);

        $context = new GlobalContext(
            originalSort: $sort,
            sort: $sort,
            orderProperties: ['test' => 'test'],
            sortableHeaderClass: 'sortable',
            sortableHeaderPrepend: '↑',
            sortableHeaderAppend: '↓',
            sortableHeaderAscClass: 'asc',
            sortableHeaderAscPrepend: '',
            sortableHeaderAscAppend: '',
            sortableHeaderDescClass: 'desc',
            sortableHeaderDescPrepend: '',
            sortableHeaderDescAppend: '',
            sortableLinkAttributes: [],
            sortableLinkAscClass: 'asc-link',
            sortableLinkDescClass: 'desc-link',
            pageToken: null,
            pageSize: 10,
            multiSort: false,
            urlConfig: new UrlConfig(),
            urlCreator: null,
            translator: $translator,
            translationCategory: 'test'
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderHeader($column, $cell, $context);
        $content = $result->getContent()[0];
        $this->assertStringContainsString('Test', $content);
        $this->assertStringContainsString('↑', $content);
        $this->assertStringContainsString('↓', $content);
        $this->assertStringContainsString('sortable', $result->getAttributes()['class']);
    }

    public function testRenderHeaderWithAscendingSort(): void
    {
        $column = new DataColumn('test', withSorting: true);
        $cell = new Cell();
        $translator = Mock::translator('en');
        $sort = Sort::only(['test'])->withOrder(['test' => 'asc']);

        $context = new GlobalContext(
            originalSort: $sort,
            sort: $sort,
            orderProperties: ['test' => 'test'],
            sortableHeaderClass: 'sortable',
            sortableHeaderPrepend: '↑',
            sortableHeaderAppend: '↓',
            sortableHeaderAscClass: 'asc',
            sortableHeaderAscPrepend: '↑',
            sortableHeaderAscAppend: '',
            sortableHeaderDescClass: 'desc',
            sortableHeaderDescPrepend: '',
            sortableHeaderDescAppend: '',
            sortableLinkAttributes: [],
            sortableLinkAscClass: 'asc-link',
            sortableLinkDescClass: 'desc-link',
            pageToken: null,
            pageSize: 10,
            multiSort: false,
            urlConfig: new UrlConfig(),
            urlCreator: null,
            translator: $translator,
            translationCategory: 'test'
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderHeader($column, $cell, $context);
        $content = $result->getContent()[0];
        $this->assertStringContainsString('Test', $content);
        $this->assertStringContainsString('↑', $content);
        $this->assertStringNotContainsString('↓', $content);
        $this->assertStringContainsString('asc', $result->getAttributes()['class']);
    }

    public function testRenderHeaderWithDescendingSort(): void
    {
        $column = new DataColumn('test', withSorting: true);
        $cell = new Cell();
        $translator = Mock::translator('en');
        $sort = Sort::only(['test'])->withOrder(['test' => 'desc']);

        $context = new GlobalContext(
            originalSort: $sort,
            sort: $sort,
            orderProperties: ['test' => 'test'],
            sortableHeaderClass: 'sortable',
            sortableHeaderPrepend: '↑',
            sortableHeaderAppend: '↓',
            sortableHeaderAscClass: 'asc',
            sortableHeaderAscPrepend: '',
            sortableHeaderAscAppend: '',
            sortableHeaderDescClass: 'desc',
            sortableHeaderDescPrepend: '',
            sortableHeaderDescAppend: '↓',
            sortableLinkAttributes: [],
            sortableLinkAscClass: 'asc-link',
            sortableLinkDescClass: 'desc-link',
            pageToken: null,
            pageSize: 10,
            multiSort: false,
            urlConfig: new UrlConfig(),
            urlCreator: null,
            translator: $translator,
            translationCategory: 'test'
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderHeader($column, $cell, $context);
        $content = $result->getContent()[0];
        $this->assertStringContainsString('Test', $content);
        $this->assertStringNotContainsString('↑', $content);
        $this->assertStringContainsString('↓', $content);
        $this->assertStringContainsString('desc', $result->getAttributes()['class']);
    }
}
