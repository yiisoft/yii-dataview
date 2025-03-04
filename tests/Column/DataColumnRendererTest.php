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
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\Column\DataColumnRenderer;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\UrlConfig;

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
            $this->dataReader,
            [],
            [],
            $translator,
            'test'
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
            $sort,
            $sort,
            ['test' => 'test'],
            'sortable',
            '',
            '',
            'asc',
            '',
            '',
            'desc',
            '',
            '',
            [],
            'asc-link',
            'desc-link',
            null,
            10,
            false,
            new UrlConfig(),
            null,
            $translator,
            'test'
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
            $this->dataReader,
            $column,
            $data,
            1,
            0
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
}
