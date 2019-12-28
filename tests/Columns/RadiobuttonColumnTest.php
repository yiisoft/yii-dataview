<?php

namespace Yiisoft\Yii\DataView\Tests\Columns;

use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Factory\Exceptions\InvalidConfigException;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Columns\RadioButtonColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\TestCase;

/**
 * @group grid
 */
class RadiobuttonColumnTest extends TestCase
{
    public function testException(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectErrorMessage('The "name" property must be set.');
        RadioButtonColumn::widget()
            ->withName('')
            ->init();
    }

    public function testOptionsByArray(): void
    {
        $column = RadioButtonColumn::widget()
            ->withRadioOptions(
                [
                    'value' => 42,
                ]
            );
        $this->assertEquals(
            '<td><input type="radio" name="radioButtonSelection" value="42"></td>',
            $column->renderDataCell([], 1, 0)
        );
    }

    public function testOptionsByCallback(): void
    {
        $model = [
            'label' => 'label',
            'value' => 123,
        ];
        $column = RadioButtonColumn::widget()
            ->withRadioOptions(
                static function ($model) {
                    return [
                        'value' => $model['value'],
                    ];
                }
            );
        $actual = $column->renderDataCell($model, 1, 0);
        $this->assertEquals(
            '<td><input type="radio" name="radioButtonSelection" value="' . $model['value'] . '"></td>',
            $actual
        );
    }

    public function testContent(): void
    {
        $column = RadioButtonColumn::widget()
            ->withContent(
                static function ($model, $key, $index, $column) {
                    return '';
                },
                );
        $this->assertStringContainsString('<td></td>', $column->renderDataCell([], 1, 0));

        $column = RadioButtonColumn::widget()
            ->withContent(
                function ($model, $key, $index, $column) {
                    return Html::radio('radioButtonInput', false);
                }
            );
        $this->assertStringContainsString(Html::radio('radioButtonInput', false), $column->renderDataCell([], 1, 0));
    }

    public function testMultipleInGrid(): void
    {
        $this->markTestIncomplete();
        $models = [
            ['label' => 'label1', 'value' => 1],
            ['label' => 'label2', 'value' => 2, 'checked' => true],
        ];
        $dataReader = new IterableDataReader($models);

        $widget = GridView::widget()
            ->withDataReader($dataReader)
            ->withOptions(['id' => 'radio-gridview'])
            ->withColumns(
                [
                    RadioButtonColumn::widget()
                        ->withRadioOptions(
                            static function ($model) {
                                return [
                                    'value' => $model['value'],
                                    'checked' => $model['value'] === 2,
                                ];
                            }
                        ),
                ]
            );
        $actual = $widget->run();
        $this->assertEquals(
            <<<'HTML'
<div id="radio-gridview"><div class="summary">Showing <b>1-2</b> of <b>2</b> items.</div>
<table class="table table-striped table-bordered"><thead>
<tr><th>&nbsp;</th></tr>
</thead>
<tbody>
<tr data-key="0"><td><input type="radio" name="radioButtonSelection" value="1"></td></tr>
<tr data-key="1"><td><input type="radio" name="radioButtonSelection" value="2" checked></td></tr>
</tbody></table>
</div>
HTML
            ,
            $actual
        );
    }
}
