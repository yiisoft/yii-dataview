<?php

namespace Yiisoft\Yii\DataView\Tests;

use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Yii\DataView\Columns\DataColumn;
use Yiisoft\Yii\DataView\GridView;

/**
 * @group grid
 */
class GridViewTest extends TestCase
{
    /**
     * @dataProvider emptyDataProvider
     * @param mixed $emptyText
     * @param string $expectedText
     * @throws \Exception
     */
    public function testEmpty($emptyText, $expectedText): void
    {
        $html = GridView::widget()
            ->withDataReader($this->createDataReader([]))
            ->withEmptyText($emptyText)
            ->withShowHeader(false)
            ->withTableOptions(
                [
                    'class' => false,
                ]
            )
            ->withOptions(
                [
                    'id' => 'grid',
                    'class' => false,
                ]
            )
            ->run();

        $html = preg_replace("/\r|\n/", '', $html);
        $expectedHtml = "<div id=\"grid\"><table><tbody>{$expectedText}</tbody></table></div>";

        $this->assertEquals($expectedHtml, $html);
    }

    public function emptyDataProvider(): array
    {
        return [
            [null, '<tr><td colspan="0"><div class="empty">No results found.</div></td></tr>'],
            ['Empty', '<tr><td colspan="0"><div class="empty">Empty</div></td></tr>'],
            // https://github.com/yiisoft/yii2/issues/13352
            [false, '<tr><td colspan="0"><div class="empty"></div></td></tr>'],
        ];
    }

    public function testGuessColumns(): void
    {
        $row = ['id' => 1, 'name' => 'Name1', 'value' => 'Value1', 'description' => 'Description1'];

        $dataReader = $this->createDataReader([$row]);
        $grid = GridView::widget()
            ->withDataReader($dataReader)
            ->init();

        $columns = $grid->getColumns();
        $this->assertCount(count($row), $columns);

        foreach ($columns as $index => $column) {
            $this->assertInstanceOf(DataColumn::class, $column);
            $this->assertArrayHasKey($column->attribute, $row);
        }

        $row = array_merge($row, ['relation' => ['id' => 1, 'name' => 'RelationName']]);
        $row = array_merge($row, ['otherRelation' => (object)$row['relation']]);

        $dataReader = $this->createDataReader([$row]);
        $grid = GridView::widget()
            ->withDataReader($dataReader)
            ->init();

        $columns = $grid->getColumns();
        $this->assertCount(count($row) - 2, $columns);

        foreach ($columns as $index => $column) {
            $this->assertInstanceOf(DataColumn::class, $column);
            $this->assertArrayHasKey($column->attribute, $row);
            $this->assertNotEquals('relation', $column->attribute);
            $this->assertNotEquals('otherRelation', $column->attribute);
        }
    }

    public function testFooterBeforeBody(): void
    {
        $html = GridView::widget()
            ->withDataReader($this->createDataReader([]))
            ->withShowFooter(true)
            ->withOptions(
                [
                    'id' => false,
                    'class' => false,
                ]
            )
            ->run();
        $html = preg_replace("/\r|\n/", '', $html);

        $this->assertRegExp("/<\/tfoot><tbody>/", $html);
    }

    public function testFooterAfterBody(): void
    {
        $html = GridView::widget()
            ->withDataReader($this->createDataReader([]))
            ->withShowFooter(true)
            ->withOptions(
                [
                    'id' => false,
                    'class' => false,
                ]
            )
            ->withPlaceFooterAfterBody(true)
            ->run();

        $html = preg_replace("/\r|\n/", '', $html);

        $this->assertRegExp("/<\/tbody><tfoot>/", $html);
    }

    private function createDataReader(array $models)
    {
        return new IterableDataReader($models);
    }
}
