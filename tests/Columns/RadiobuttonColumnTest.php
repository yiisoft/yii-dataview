<?php
/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\DataView\Tests\Coolumns;

use yii\data\ArrayDataProvider;
use Yiisoft\Yii\DataView\Columns\RadioButtonColumn;
use Yiisoft\Yii\DataView\GridView;
use yii\helpers\Html;
use yii\helpers\Yii;
use yii\tests\TestCase;
use yii\web\Request;

/**
 * Class RadiobuttonColumnTest.
 *
 * @group grid
 *
 * @since 2.0.11
 */
class RadiobuttonColumnTest extends TestCase
{
    /**
     * @expectedException \Yiisoft\Factory\Exceptions\InvalidConfigException
     * @expectedExceptionMessage The "name" property must be set.
     */
    public function testException()
    {
        Yii::createObject([
            '__class' => RadioButtonColumn::class,
            'name'    => null,
        ]);
    }

    public function testOptionsByArray()
    {
        $column = Yii::createObject([
            '__class'      => RadioButtonColumn::class,
            'radioOptions' => [
                'value' => 42,
            ],
        ]);
        $this->assertEquals('<td><input type="radio" name="radioButtonSelection" value="42"></td>', $column->renderDataCell([], 1, 0));
    }

    public function testOptionsByCallback()
    {
        $model = [
            'label' => 'label',
            'value' => 123,
        ];
        $column = Yii::createObject([
            '__class'      => RadioButtonColumn::class,
            'radioOptions' => function ($model) {
                return [
                    'value' => $model['value'],
                ];
            },
        ]);
        $actual = $column->renderDataCell($model, 1, 0);
        $this->assertEquals('<td><input type="radio" name="radioButtonSelection" value="'.$model['value'].'"></td>', $actual);
    }

    public function testContent()
    {
        $column = Yii::createObject([
            '__class' => RadioButtonColumn::class,
            'content' => function ($model, $key, $index, $column) {
            },
        ]);
        $this->assertContains('<td></td>', $column->renderDataCell([], 1, 0));

        $column = Yii::createObject([
            '__class' => RadioButtonColumn::class,
            'content' => function ($model, $key, $index, $column) {
                return Html::radio('radioButtonInput', false);
            },
        ]);
        $this->assertContains(Html::radio('radioButtonInput', false), $column->renderDataCell([], 1, 0));
    }

    public function testMultipleInGrid()
    {
        $this->mockApplication();
        Yii::setAlias('@webroot', '@yii/tests/runtime');
        Yii::setAlias('@web', 'http://localhost/');
        Yii::getApp()->assetManager->bundles['yii\web\JqueryAsset'] = false;
        $this->container->set('request', Yii::createObject([
            '__class' => Request::class,
            'url'     => '/abc',
        ]));

        $models = [
            ['label' => 'label1', 'value' => 1],
            ['label' => 'label2', 'value' => 2, 'checked' => true],
        ];
        $grid = Yii::createObject([
            '__class'      => GridView::class,
            'dataProvider' => Yii::createObject([
                '__class'   => ArrayDataProvider::class,
                'allModels' => $models,
            ]),
            'options' => ['id' => 'radio-gridview'],
            'columns' => [
                [
                    '__class'      => RadioButtonColumn::class,
                    'radioOptions' => function ($model) {
                        return [
                            'value'   => $model['value'],
                            'checked' => $model['value'] == 2,
                        ];
                    },
                ],
            ],
        ]);
        $actual = $grid->run();
        $this->assertEqualsWithoutLE(<<<'HTML'
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
            , $actual);
    }
}
