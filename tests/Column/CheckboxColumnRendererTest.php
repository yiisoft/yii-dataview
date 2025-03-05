<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Html\Tag\Input\Checkbox;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;
use Yiisoft\Yii\DataView\Column\CheckboxColumn;
use Yiisoft\Yii\DataView\Column\CheckboxColumnRenderer;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\UrlConfig;

final class CheckboxColumnRendererTest extends TestCase
{
    private CheckboxColumnRenderer $renderer;
    private Cell $cell;
    private GlobalContext $globalContext;
    private IterableDataReader $dataReader;

    protected function setUp(): void
    {
        $this->renderer = new CheckboxColumnRenderer();
        $this->cell = new Cell();
        $this->dataReader = new IterableDataReader([
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Mary'],
        ]);
        $this->globalContext = new GlobalContext(
            $this->dataReader,
            [],
            [],
            Mock::translator('en'),
            'test'
        );
    }

    public function testRenderColumn(): void
    {
        $column = new CheckboxColumn(columnAttributes: ['class' => 'test-class']);
        $result = $this->renderer->renderColumn($column, $this->cell, $this->globalContext);
        $this->assertSame(['class' => 'test-class'], $result->getAttributes());
    }

    public function testRenderHeaderWithoutHeaderAndSingleSelection(): void
    {
        $column = new CheckboxColumn(multiple: false);
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
            Mock::translator('en'),
            'test'
        );
        $result = $this->renderer->renderHeader($column, $this->cell, $context);
        $this->assertNull($result);
    }

    public function testRenderHeaderWithoutHeaderAndMultipleSelection(): void
    {
        $column = new CheckboxColumn(multiple: true);
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
            Mock::translator('en'),
            'test'
        );
        $result = $this->renderer->renderHeader($column, $this->cell, $context);
        $this->assertNotNull($result);
        $this->assertStringContainsString('checkbox-selection-all', (string) $result->getContent()[0]);
    }

    public function testRenderHeaderWithCustomHeader(): void
    {
        $column = new CheckboxColumn(
            header: 'Custom Header',
            headerAttributes: ['class' => 'header-class']
        );
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
            Mock::translator('en'),
            'test'
        );
        $result = $this->renderer->renderHeader($column, $this->cell, $context);
        $this->assertNotNull($result);
        $this->assertSame('Custom Header', $result->getContent()[0]);
        $this->assertSame(['class' => 'header-class'], $result->getAttributes());
    }

    public function testRenderBodyWithDefaultNameAndValue(): void
    {
        $column = new CheckboxColumn();
        $context = new DataContext(
            $this->dataReader,
            $column,
            ['id' => 1, 'name' => 'John'],
            1,
            0
        );
        $result = $this->renderer->renderBody($column, $this->cell, $context);
        $content = (string) $result->getContent()[0];
        $this->assertStringContainsString('name="checkbox-selection"', $content);
        $this->assertStringContainsString('value="1"', $content);
        $this->assertFalse($result->shouldEncode());
    }

    public function testRenderBodyWithCustomNameAndValue(): void
    {
        $column = new CheckboxColumn(
            inputAttributes: [
                'name' => 'custom-name',
                'value' => 'custom-value',
                'class' => 'custom-class',
            ]
        );
        $context = new DataContext(
            $this->dataReader,
            $column,
            ['id' => 1, 'name' => 'John'],
            1,
            0
        );
        $result = $this->renderer->renderBody($column, $this->cell, $context);
        $content = (string) $result->getContent()[0];
        $this->assertStringContainsString('name="custom-name"', $content);
        $this->assertStringContainsString('value="custom-value"', $content);
        $this->assertStringContainsString('class="custom-class"', $content);
    }

    public function testRenderBodyWithCustomContent(): void
    {
        $column = new CheckboxColumn(
            content: static fn(Checkbox $input, DataContext $context): string => "<div>{$input->render()}</div>"
        );
        $context = new DataContext(
            $this->dataReader,
            $column,
            ['id' => 1, 'name' => 'John'],
            1,
            0
        );
        $result = $this->renderer->renderBody($column, $this->cell, $context);
        $content = (string) $result->getContent()[0];
        $this->assertStringStartsWith('<div>', $content);
        $this->assertStringEndsWith('</div>', $content);
    }

    public function testRenderFooterWithContent(): void
    {
        $column = new CheckboxColumn(footer: 'Test Footer');
        $result = $this->renderer->renderFooter($column, $this->cell, $this->globalContext);
        $this->assertSame('Test Footer', $result->getContent()[0]);
    }

    public function testRenderFooterWithoutContent(): void
    {
        $column = new CheckboxColumn();
        $result = $this->renderer->renderFooter($column, $this->cell, $this->globalContext);
        $this->assertEmpty($result->getContent());
    }
}
