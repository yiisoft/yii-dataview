<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use Yiisoft\Data\Reader\Sort;
use Yiisoft\VarDumper\VarDumper;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\TestCase;

final class DataColumnTest extends TestCase
{
    public function testRender(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView($this->createColumns());
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = $gridView->render();

        $expected = <<<'HTML'
        <thead>
        <tr><th>Id</th><th>Name</th><th>Total</th></tr>
        </thead>
        <tbody>
        <tr data-key="0"><td>1</td><td>tests 1</td><td>10</td></tr>
        <tr data-key="1"><td>2</td><td>tests 2</td><td>20</td></tr>
        <tr data-key="2"><td>3</td><td>tests 3</td><td>30</td></tr>
        <tr data-key="3"><td>4</td><td>tests 4</td><td>40</td></tr>
        <tr data-key="4"><td>5</td><td>tests 5</td><td>50</td></tr>
        </tbody>
        HTML;

        $this->assertStringContainsString($expected, $html);
    }

    public function testRenderColumString(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView($this->createColumnsString());
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = $gridView->render();

        $expected = <<<'HTML'
        <thead>
        <tr><th>Id</th><th>Name</th><th>Total</th></tr>
        </thead>
        <tbody>
        <tr data-key="0"><td>1</td><td>tests 1</td><td>10</td></tr>
        <tr data-key="1"><td>2</td><td>tests 2</td><td>20</td></tr>
        <tr data-key="2"><td>3</td><td>tests 3</td><td>30</td></tr>
        <tr data-key="3"><td>4</td><td>tests 4</td><td>40</td></tr>
        <tr data-key="4"><td>5</td><td>tests 5</td><td>50</td></tr>
        </tbody>
        HTML;

        $this->assertStringContainsString($expected, $html);
    }

    public function testRenderColumMissingLabel(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView($this->createColumnsMissingLabel());
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = $gridView->render();

        $expected = <<<'HTML'
        <thead>
        <tr><th>Id</th><th>Name</th><th>Total</th></tr>
        </thead>
        <tbody>
        <tr data-key="0"><td>1</td><td>tests 1</td><td>10</td></tr>
        <tr data-key="1"><td>2</td><td>tests 2</td><td>20</td></tr>
        <tr data-key="2"><td>3</td><td>tests 3</td><td>30</td></tr>
        <tr data-key="3"><td>4</td><td>tests 4</td><td>40</td></tr>
        <tr data-key="4"><td>5</td><td>tests 5</td><td>50</td></tr>
        </tbody>
        HTML;

        $this->assertStringContainsString($expected, $html);
    }

    private function createColumns(): array
    {
        return [
            [
                'attribute()' => ['id'],
                'label()' => ['Id'],
            ],
            [
                'attribute()' => ['name'],
                'label()' => ['Name'],
            ],
            [
                'attribute()' => ['total'],
                'label()' => ['Total'],
            ],
        ];
    }

    private function createColumnsString(): array
    {
        return [
            'id:text:Id',
            'name:text:Name',
            'total:text:Total',
        ];
    }

    private function createColumnsMissingLabel(): array
    {
        return [
            [
                'attribute()' => ['id'],
            ],
            [
                'attribute()' => ['name'],
            ],
            [
                'attribute()' => ['total'],
            ],
        ];
    }
}
