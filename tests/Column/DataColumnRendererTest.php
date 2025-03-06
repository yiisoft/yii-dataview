<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Validator\Validator;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;
use Yiisoft\Yii\DataView\Column\Base\FilterContext;
use Yiisoft\Yii\DataView\Column\Base\MakeFilterContext;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\Column\DataColumnRenderer;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\UrlConfig;
use Yiisoft\Validator\Rule\Number;

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
        $translator = Mock::translator('en');

        $context = new GlobalContext(
            dataReader: $this->dataReader,
            pathArguments: [],
            queryParameters: [],
            translator: $translator,
            translationCategory: 'test'
        );

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

        $context = new HeaderContext(
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
        $date = new \DateTime('2025-03-06 02:00:22');
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

        $urlParameterProvider = new class () implements \Yiisoft\Yii\DataView\UrlParameterProviderInterface {
            public function get(string $name, int $type): ?string
            {
                return 'active';
            }
        };

        $context = new FilterContext(
            'filter-form',
            new \Yiisoft\Validator\Result(),
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
            filterFactory: \Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory::class
        );

        $urlParameterProvider = new class () implements \Yiisoft\Yii\DataView\UrlParameterProviderInterface {
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
            new \Yiisoft\Validator\Result(),
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

        $urlParameterProvider = new class () implements \Yiisoft\Yii\DataView\UrlParameterProviderInterface {
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
            new \Yiisoft\Validator\Result(),
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

        $urlParameterProvider = new class () implements \Yiisoft\Yii\DataView\UrlParameterProviderInterface {
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
            new \Yiisoft\Validator\Result(),
            $urlParameterProvider
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->makeFilter($column, $context);
        $this->assertNull($result);
    }
}
