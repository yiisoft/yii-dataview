<?php

declare(strict_types=1);

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
     *
     * @param mixed $emptyText
     * @param string $expectedText
     *
     * @throws \Exception
     */
    public function testEmpty($emptyText, $expectedText): void
    {
        $html = GridView::widget()
            ->dataReader($this->createDataReader([]))
            ->emptyText($emptyText)
            ->showHeader(false)
            ->tableOptions(
                [
                    'class' => false,
                ]
            )
            ->options(
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
            ->dataReader($dataReader);
        $grid->run();

        $columns = $grid->getColumns();
        $this->assertCount(count($row), $columns);

        foreach ($columns as $index => $column) {
            /* @var $column DataColumn */
            $this->assertInstanceOf(DataColumn::class, $column);
            $this->assertArrayHasKey($column->getAttribute(), $row);
        }

        $row = array_merge($row, ['relation' => ['id' => 1, 'name' => 'RelationName']]);
        $row = array_merge($row, ['otherRelation' => (object)$row['relation']]);

        $dataReader = $this->createDataReader([$row]);
        $grid = GridView::widget()
            ->dataReader($dataReader);
        $grid->run();

        $columns = $grid->getColumns();
        $this->assertCount(count($row) - 2, $columns);

        foreach ($columns as $index => $column) {
            $this->assertInstanceOf(DataColumn::class, $column);
            $this->assertArrayHasKey($column->getAttribute(), $row);
            $this->assertNotEquals('relation', $column->getAttribute());
            $this->assertNotEquals('otherRelation', $column->getAttribute());
        }
    }

    public function testFooterBeforeBody(): void
    {
        $html = GridView::widget()
            ->dataReader($this->createDataReader([]))
            ->showFooter(true)
            ->options(
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
            ->dataReader($this->createDataReader([]))
            ->showFooter(true)
            ->options(
                [
                    'id' => false,
                    'class' => false,
                ]
            )
            ->placeFooterAfterBody(true)
            ->run();

        $html = preg_replace("/\r|\n/", '', $html);

        $this->assertRegExp("/<\/tbody><tfoot>/", $html);
    }

    private function createDataReader(array $models): IterableDataReader
    {
        return new IterableDataReader($models);
    }
}
