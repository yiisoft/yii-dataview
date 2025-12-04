<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView\Column;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Di\Container;
use Yiisoft\Yii\DataView\GridView\Column\SerialColumn;
use Yiisoft\Yii\DataView\GridView\Column\SerialColumnRenderer;
use Yiisoft\Yii\DataView\GridView\GridView;

final class SerialColumnTest extends TestCase
{
    public function testBase(): void
    {
        $html = $this->createGridView([['name' => 'John'], ['name' => 'Jane'], ['name' => 'Bob']])
            ->columns(new SerialColumn())
            ->render();

        $this->assertSame(
            <<<HTML
            <table>
            <thead>
            <tr>
            <th>#</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            </tr>
            <tr>
            <td>2</td>
            </tr>
            <tr>
            <td>3</td>
            </tr>
            </tbody>
            </table>
            HTML,
            $html,
        );
    }

    public function testOffsetPaginator(): void
    {
        $dataReader = new IterableDataReader([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
            ['id' => 4],
            ['id' => 5],
        ]);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(2)->withCurrentPage(2);
        $html = $this->createGridView($paginator)
            ->columns(new SerialColumn())
            ->render();

        $this->assertSame(
            <<<HTML
            <table>
            <thead>
            <tr>
            <th>#</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>3</td>
            </tr>
            <tr>
            <td>4</td>
            </tr>
            </tbody>
            </table>
            HTML,
            $html,
        );
    }

    public function testKeysetPaginator(): void
    {
        $dataReader = (new IterableDataReader([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
            ['id' => 4],
            ['id' => 5],
        ]))->withSort(Sort::any(['id']));
        $paginator = (new KeysetPaginator($dataReader))->withPageSize(2)->withToken(PageToken::next('2'));
        $html = $this->createGridView($paginator)
            ->columns(new SerialColumn())
            ->render();

        $this->assertSame(
            <<<HTML
            <table>
            <thead>
            <tr>
            <th>#</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            </tr>
            <tr>
            <td>2</td>
            </tr>
            </tbody>
            </table>
            HTML,
            $html,
        );
    }

    public function testHeader(): void
    {
        $html = $this->createGridView([['name' => 'John']])
            ->columns(new SerialColumn(header: 'Row Number'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th>Row Number</th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testBodyAttributes(): void
    {
        $html = $this->createGridView([['name' => 'John']])
            ->columns(
                new SerialColumn(
                    bodyAttributes: ['class' => 'serial-body'],
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td class="serial-body">1</td>
            HTML,
            $html,
        );
    }

    public function testColumnAttributes(): void
    {
        $html = $this->createGridView([['name' => 'John']])
            ->columns(
                new SerialColumn(
                    columnAttributes: ['class' => 'serial-col'],
                ),
            )
            ->columnGrouping()
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <colgroup>
            <col class="serial-col">
            </colgroup>
            HTML,
            $html,
        );
    }

    public function testFooter(): void
    {
        $html = $this->createGridView([['name' => 'John']])
            ->columns(
                new SerialColumn(
                    footer: 'Total Rows',
                ),
            )
            ->enableFooter()
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tfoot>
            <tr>
            <td>Total Rows</td>
            </tr>
            </tfoot>
            HTML,
            $html,
        );
    }

    public function testVisible(): void
    {
        $html = $this->createGridView([['name' => 'John']])
            ->columns(new SerialColumn(visible: false))
            ->render();

        $this->assertSame(
            <<<HTML
            <table>
            <thead>
            <tr></tr>
            </thead>
            <tbody>
            <tr></tr>
            </tbody>
            </table>
            HTML,
            $html,
        );
    }

    private function createGridView(array|ReadableDataInterface $data = []): GridView
    {
        $dataReader = $data instanceof ReadableDataInterface ? $data : new IterableDataReader($data);

        return (new GridView(new Container()))
            ->layout('{items}')
            ->containerTag(null)
            ->dataReader($dataReader)
            ->addColumnRendererConfigs([
                SerialColumnRenderer::class => [],
            ]);
    }
}
