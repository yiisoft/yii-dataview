<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Validator;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\Column\DataColumnRenderer;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\UrlConfig;

final class DataColumnRendererTest extends TestCase
{
    use TestTrait;

    private ContainerInterface $filterFactoryContainer;
    private TranslatorInterface $translator;
    private IterableDataReader $dataReader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filterFactoryContainer = $this->createMock(ContainerInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->translator->method('translate')->willReturnArgument(0);

        $this->dataReader = new IterableDataReader([
            ['id' => 1, 'name' => 'John', 'age' => 20],
            ['id' => 2, 'name' => 'Mary', 'age' => 21],
        ]);
    }

    public function testRenderColumn(): void
    {
        $column = new DataColumn('test');
        $cell = new Cell();
        $context = new GlobalContext(
            $this->dataReader,
            [],
            [],
            $this->translator,
            'test'
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderColumn($column, $cell, $context);

        $this->assertNotNull($result);
    }

    public function testRenderHeader(): void
    {
        $column = new DataColumn('test', 'Test Header');
        $cell = new Cell();

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
            $this->translator,
            'test'
        );

        $renderer = new DataColumnRenderer(
            $this->filterFactoryContainer,
            new Validator()
        );

        $result = $renderer->renderHeader($column, $cell, $context);

        $this->assertNotNull($result);
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

        $this->assertNotNull($result);
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
