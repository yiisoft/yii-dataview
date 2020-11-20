<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Columns;

use Yiisoft\Yii\DataView\Columns\ActionColumn;
use Yiisoft\Yii\DataView\Tests\TestCase;

/**
 * @group grid
 */
class ActionColumnTest extends TestCase
{
    public function testDefaultButtons(): void
    {
        $column = ActionColumn::widget();
        $this->assertEquals(['view', 'update', 'delete'], array_keys($column->getButtons()));
    }

    public function testOneButtonMatched(): void
    {
        $column = ActionColumn::widget()->template('{show} {edit} {delete}');
        $this->assertEquals(['delete'], array_keys($column->getButtons()));
    }

    public function testNoMatchedResults(): void
    {
        $column = ActionColumn::widget()->template('{show} {edit} {remove}');
        $this->assertEmpty($column->getButtons());
    }

    public function testDashInButtonPlaceholder(): void
    {
        $column = ActionColumn::widget()->template('{show-items}');
        $this->assertEmpty($column->getButtons());
    }

    public function testRenderDataCell(): void
    {
        $column = ActionColumn::widget()
            ->urlCreator(
                static function ($model, $key, $index) {
                    return 'http://test.com';
                }
            );
        $columnContents = $column->renderDataCell(['id' => 1], 1, 0);
        $viewButton = '<a href="http://test.com" title="View" aria-label="View" data-name="view"><span class="glyphicon glyphicon-eye-open"></span></a>';
        $updateButton = '<a href="http://test.com" title="Update" aria-label="Update" data-name="update"><span class="glyphicon glyphicon-pencil"></span></a>';
        $deleteButton = '<a href="http://test.com" title="Delete" aria-label="Delete" data-name="delete" data-confirm="Are you sure you want to delete this item?" data-method="post"><span class="glyphicon glyphicon-trash"></span></a>';
        $expectedHtml = "<td>$viewButton $updateButton $deleteButton</td>";
        $this->assertEquals($expectedHtml, $columnContents);

        $column = ActionColumn::widget()
            ->urlCreator(
                static function ($model, $key, $index) {
                    return 'http://test.com';
                }
            )
            ->template('{update}')
            ->buttons(
                [
                    'update' => static function ($url, $model, $key) {
                        return 'update_button';
                    },
                ]
            );

        //test default visible button
        $columnContents = $column->renderDataCell(['id' => 1], 1, 0);
        $this->assertStringContainsString('update_button', $columnContents);

        //test visible button
        $column->visibleButtons(
            [
                'update' => true,
            ]
        );
        $columnContents = $column->renderDataCell(['id' => 1], 1, 0);
        $this->assertStringContainsString('update_button', $columnContents);

        //test visible button (condition is callback)
        $column->visibleButtons(
            [
                'update' => static function ($model, $key, $index) {
                    return $model['id'] === 1;
                },
            ]
        );
        $columnContents = $column->renderDataCell(['id' => 1], 1, 0);
        $this->assertStringContainsString('update_button', $columnContents);

        //test invisible button
        $column->visibleButtons(
            [
                'update' => false,
            ]
        );
        $columnContents = $column->renderDataCell(['id' => 1], 1, 0);
        $this->assertStringNotContainsString('update_button', $columnContents);

        //test invisible button (condition is callback)
        $column->visibleButtons(
            [
                'update' => static function ($model, $key, $index) {
                    return $model['id'] !== 1;
                },
            ]
        );
        $columnContents = $column->renderDataCell(['id' => 1], 1, 0);
        $this->assertStringNotContainsString('update_button', $columnContents);
    }
}
