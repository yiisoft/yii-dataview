<?php
/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\DataView\Tests\Coolumns;

use yii\data\ArrayDataProvider;
use Yiisoft\Yii\DataView\Columns\CheckboxColumn;
use Yiisoft\Yii\DataView\GridView;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Yii;
use yii\tests\framework\i18n\IntlTestHelper;
use yii\tests\TestCase;

/**
 * @group grid
 */
class CheckboxColumnTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        IntlTestHelper::resetIntlStatus();
        $this->mockApplication();
        Yii::setAlias('@webroot', '@yii/tests/runtime');
        Yii::setAlias('@web', 'http://localhost/');
        FileHelper::createDirectory(Yii::getAlias('@webroot/assets'));
        Yii::getApp()->assetManager->bundles['yii\web\JqueryAsset'] = false;
    }

    public function testInputName()
    {
        $column = Yii::createObject([
            '__class' => CheckboxColumn::class,
            'name'    => 'selection',
            'grid'    => $this->getGrid(),
        ]);
        $this->assertContains('name="selection_all"', $column->renderHeaderCell());

        $column = Yii::createObject([
            '__class' => CheckboxColumn::class,
            'name'    => 'selections[]',
            'grid'    => $this->getGrid(),
        ]);
        $this->assertContains('name="selections_all"', $column->renderHeaderCell());

        $column = Yii::createObject([
            '__class' => CheckboxColumn::class,
            'name'    => 'MyForm[grid1]',
            'grid'    => $this->getGrid(),
        ]);
        $this->assertContains('name="MyForm[grid1_all]"', $column->renderHeaderCell());

        $column = Yii::createObject([
            '__class' => CheckboxColumn::class,
            'name'    => 'MyForm[grid1][]',
            'grid'    => $this->getGrid(),
        ]);
        $this->assertContains('name="MyForm[grid1_all]"', $column->renderHeaderCell());

        $column = Yii::createObject([
            '__class' => CheckboxColumn::class,
            'name'    => 'MyForm[grid1][key]',
            'grid'    => $this->getGrid(),
        ]);
        $this->assertContains('name="MyForm[grid1][key_all]"', $column->renderHeaderCell());

        $column = Yii::createObject([
            '__class' => CheckboxColumn::class,
            'name'    => 'MyForm[grid1][key][]',
            'grid'    => $this->getGrid(),
        ]);
        $this->assertContains('name="MyForm[grid1][key_all]"', $column->renderHeaderCell());
    }

    public function testInputValue()
    {
        $column = Yii::createObject([
            '__class' => CheckboxColumn::class,
            'grid'    => $this->getGrid(),
        ]);
        $this->assertContains('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertContains('value="42"', $column->renderDataCell([], 42, 0));
        $this->assertContains('value="[1,42]"', $column->renderDataCell([], [1, 42], 0));

        $column = Yii::createObject([
            '__class'         => CheckboxColumn::class,
            'checkboxOptions' => ['value' => 42],
            'grid'            => $this->getGrid(),
        ]);
        $this->assertNotContains('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertContains('value="42"', $column->renderDataCell([], 1, 0));

        $column = Yii::createObject([
            '__class'         => CheckboxColumn::class,
            'checkboxOptions' => function ($model, $key, $index, $column) {
                return [];
            },
            'grid' => $this->getGrid(),
        ]);
        $this->assertContains('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertContains('value="42"', $column->renderDataCell([], 42, 0));
        $this->assertContains('value="[1,42]"', $column->renderDataCell([], [1, 42], 0));

        $column = Yii::createObject([
            '__class'         => CheckboxColumn::class,
            'checkboxOptions' => function ($model, $key, $index, $column) {
                return ['value' => 42];
            },
            'grid' => $this->getGrid(),
        ]);
        $this->assertNotContains('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertContains('value="42"', $column->renderDataCell([], 1, 0));
    }

    public function testContent()
    {
        $column = Yii::createObject([
            '__class' => CheckboxColumn::class,
            'content' => function ($model, $key, $index, $column) {
            },
            'grid' => $this->getGrid(),
        ]);
        $this->assertContains('<td></td>', $column->renderDataCell([], 1, 0));

        $column = Yii::createObject([
            '__class' => CheckboxColumn::class,
            'content' => function ($model, $key, $index, $column) {
                return Html::checkBox('checkBoxInput', false);
            },
            'grid' => $this->getGrid(),
        ]);
        $this->assertContains(Html::checkBox('checkBoxInput', false), $column->renderDataCell([], 1, 0));
    }

    /**
     * @return GridView a mock gridview
     */
    protected function getGrid()
    {
        return Yii::createObject([
            '__class'      => GridView::class,
            'dataProvider' => Yii::createObject([
                '__class'    => ArrayDataProvider::class,
                'allModels'  => [],
                'totalCount' => 0,
            ]),
        ]);
    }
}
