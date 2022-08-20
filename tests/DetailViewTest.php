<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests;

use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\Exception\InvalidConfigException;

final class DetailViewTest extends TestCase
{
    public function testAttributes(): void
    {
        DetailView::counter(0);

        $html = <<<'HTML'
        <table id="w1-detailview" class="table table-striped table-bordered detail-view">
        <tr><th>Id</th><td>1</td></tr>
        <tr><th>Username</th><td>tests 1</td></tr>
        <tr><th>Total</th><td>10</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->attributes(
                [
                    ['attribute' => 'id'],
                    ['attribute' => 'username'],
                    ['attribute' => 'total'],
                ],
            )
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10']);
        $this->assertEqualsWithoutLE($html, $detailView->render());
    }

    public function testAttributesException(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(
            'The attribute configuration requires the "attribute" element to determine the value and display label.'
        );
        $detailView = DetailView::widget()
            ->attributes([['label' => 'id']])
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
            ->render();
    }

    public function testAttributesFormatException(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(
            'The attribute must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"'
        );
        $detailView = DetailView::widget()
            ->attributes([''])
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
            ->render();
    }

    public function testAttributesFormatString(): void
    {
        DetailView::counter(0);

        $html = <<<'HTML'
        <table id="w1-detailview" class="table table-striped table-bordered detail-view">
        <tr><th>Id</th><td>1</td></tr>
        <tr><th>Username</th><td>tests 1</td></tr>
        <tr><th>Total</th><td>10</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->attributes(['id', 'username', 'total'])
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10']);
        $this->assertEqualsWithoutLE($html, $detailView->render());
    }

    public function testCaptionOptions(): void
    {
        DetailView::counter(0);

        $html = <<<'HTML'
        <table id="w1-detailview" class="table table-striped table-bordered detail-view">
        <tr><th class="text-success">Id</th><td>1</td></tr>
        <tr><th class="text-success">Username</th><td>tests 1</td></tr>
        <tr><th class="text-success">Total</th><td>10</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->attributes(
                [
                    ['attribute' => 'id'],
                    ['attribute' => 'username'],
                    ['attribute' => 'total'],
                ],
            )
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
            ->captionOptions(['class' => 'text-success']);
        $this->assertEqualsWithoutLE($html, $detailView->render());

        $html = <<<'HTML'
        <table id="w2-detailview" class="table table-striped table-bordered detail-view">
        <tr><th class="text-success">Id</th><td>1</td></tr>
        <tr><th class="text-success">Username</th><td>tests 1</td></tr>
        <tr><th class="text-success" style="width:20px;">Total</th><td>10</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->attributes(
                [
                    ['attribute' => 'id'],
                    ['attribute' => 'username'],
                    ['attribute' => 'total', 'captionOptions' => ['style' => 'width:20px;']],
                ],
            )
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
            ->captionOptions(['class' => 'text-success']);
        $this->assertEqualsWithoutLE($html, $detailView->render());
    }

    public function testContentOptions(): void
    {
        DetailView::counter(0);

        $html = <<<'HTML'
        <table id="w1-detailview" class="table table-striped table-bordered detail-view">
        <tr><th>Id</th><td class="text-success">1</td></tr>
        <tr><th>Username</th><td class="text-success">tests 1</td></tr>
        <tr><th>Total</th><td class="text-success">10</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->attributes(
                [
                    ['attribute' => 'id'],
                    ['attribute' => 'username'],
                    ['attribute' => 'total'],
                ],
            )
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
            ->contentOptions(['class' => 'text-success']);
        $this->assertEqualsWithoutLE($html, $detailView->render());

        $html = <<<'HTML'
        <table id="w2-detailview" class="table table-striped table-bordered detail-view">
        <tr><th>Id</th><td class="text-success">1</td></tr>
        <tr><th>Username</th><td class="text-success">tests 1</td></tr>
        <tr><th>Total</th><td class="text-success" style="width:20px;">10</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->attributes(
                [
                    ['attribute' => 'id'],
                    ['attribute' => 'username'],
                    ['attribute' => 'total', 'contentOptions' => ['style' => 'width:20px;']],
                ],
            )
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
            ->contentOptions(['class' => 'text-success']);
        $this->assertEqualsWithoutLE($html, $detailView->render());
    }

    public function testLabel(): void
    {
        DetailView::counter(0);

        $html = <<<'HTML'
        <table id="w1-detailview" class="table table-striped table-bordered detail-view">
        <tr><th>id</th><td>1</td></tr>
        <tr><th>username</th><td>tests 1</td></tr>
        <tr><th>total</th><td>10</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->attributes(
                [
                    ['attribute' => 'id', 'label' => 'id'],
                    ['attribute' => 'username', 'label' => 'username'],
                    ['attribute' => 'total', 'label' => 'total'],
                ],
            )
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10']);
        $this->assertEqualsWithoutLE($html, $detailView->render());
    }

    public function testModelException(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('The "model" property must be either an array or an object.');
        $detailView = DetailView::widget()->model('exception');
    }

    public function testOptions(): void
    {
        DetailView::counter(0);

        $html = <<<'HTML'
        <table id="w1-detailview" class="table table-hoverable">
        <tr><th>Id</th><td>1</td></tr>
        <tr><th>Username</th><td>tests 1</td></tr>
        <tr><th>Total</th><td>10</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->attributes(
                [
                    ['attribute' => 'id'],
                    ['attribute' => 'username',],
                    ['attribute' => 'total'],
                ],
            )
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
            ->options(['class' => 'table table-hoverable']);
        $this->assertEqualsWithoutLE($html, $detailView->render());
    }

    public function testRender(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Please specify the "model" property.');
        $detailView = DetailView::widget()->render();
    }

    public function testRowOptions(): void
    {
        DetailView::counter(0);

        $html = <<<'HTML'
        <table id="w1-detailview" class="table table-striped table-bordered detail-view">
        <tr class="alert alert-sucess"><th>Id</th><td>1</td></tr>
        <tr class="alert alert-sucess"><th>Username</th><td>tests 1</td></tr>
        <tr class="alert alert-sucess"><th>Total</th><td>10</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->attributes(
                [
                    ['attribute' => 'id'],
                    ['attribute' => 'username'],
                    ['attribute' => 'total'],
                ],
            )
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
            ->rowOptions(['class' => 'alert alert-sucess']);
        $this->assertEqualsWithoutLE($html, $detailView->render());

        $html = <<<'HTML'
        <table id="w2-detailview" class="table table-striped table-bordered detail-view">
        <tr class="alert alert-sucess"><th>Id</th><td>1</td></tr>
        <tr class="alert alert-sucess"><th>Username</th><td>tests 1</td></tr>
        <tr class="alert alert-sucess text-center"><th>Total</th><td>10</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->attributes(
                [
                    ['attribute' => 'id'],
                    ['attribute' => 'username'],
                    ['attribute' => 'total', 'rowOptions' => ['class' => 'text-center']],
                ],
            )
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
            ->rowOptions(['class' => 'alert alert-sucess']);
        $this->assertEqualsWithoutLE($html, $detailView->render());
    }

    public function testTemplate(): void
    {
        DetailView::counter(0);

        $html = <<<'HTML'
        <table id="w1-detailview" class="table table-striped table-bordered detail-view">
        <tr class="text-center"><th>Id</th><td>1</td></tr>
        <tr class="text-center"><th>Username</th><td>tests 1</td></tr>
        <tr class="text-center"><th>Total</th><td>10</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->attributes(
                [
                    ['attribute' => 'id'],
                    ['attribute' => 'username',],
                    ['attribute' => 'total'],
                ],
            )
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
            ->template(
                '<tr class="text-center"><th{captionOptions}>{label}</th><td{contentOptions}>{value}</td></tr>'
            );
        $this->assertEqualsWithoutLE($html, $detailView->render());
    }

    public function testValue(): void
    {
        DetailView::counter(0);

        $html = <<<'HTML'
        <table id="w1-detailview" class="table table-striped table-bordered detail-view">
        <tr><th>Id</th><td>1</td></tr>
        <tr><th>Username</th><td>testMe</td></tr>
        <tr><th>Total</th><td>2,000</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->attributes(
                [
                    ['attribute' => 'id'],
                    ['attribute' => 'username', 'value' => static fn () => 'testMe'],
                    ['attribute' => 'total:number'],
                ],
            )
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => 2000]);
        $this->assertEqualsWithoutLE($html, $detailView->render());
    }

    public function testVisible(): void
    {
        DetailView::counter(0);

        $html = <<<'HTML'
        <table id="w1-detailview" class="table table-striped table-bordered detail-view">
        <tr><th>Id</th><td>1</td></tr>
        <tr><th>Total</th><td>10</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->attributes(
                [
                    ['attribute' => 'id'],
                    ['attribute' => 'username', 'visible' => false],
                    ['attribute' => 'total'],
                ],
            )
            ->model(['id' => 1, 'username' => 'tests 1', 'total' => '10']);
        $this->assertEqualsWithoutLE($html, $detailView->render());
    }

    public function testEmptyValue(): void
    {
        DetailView::counter(0);

        $html = <<<'HTML'
        <table id="w1-detailview" class="table table-striped table-bordered detail-view">
        <tr><th>Id</th><td>1</td></tr>
        <tr><th>IsAdmin</th><td>False</td></tr>
        <tr><th>Total</th><td>0</td></tr>
        </table></div>
        HTML;        
        
        $detailView = DetailView::widget()
            ->emptyValue(null)
            ->attributes(
                [
                    ['attribute' => 'id'],
                    ['attribute' => 'username'],
                    ['attribute' => 'isAdmin', 'format' => static function($value) { return $value ? 'True' : 'False'; }],
                    ['attribute' => 'total:number'],
                ],
            )
            ->model(['id' => 1, 'username' => '', 'isAdmin' => false, 'total' => 0]);        
        
        
        $this->assertEqualsWithoutLE($html, $detailView->render());

        DetailView::counter(0);

        $html = <<<'HTML'
        <table id="w1-detailview" class="table table-striped table-bordered detail-view">
        <tr><th>Id</th><td>1</td></tr>
        <tr><th>Username</th><td> - </td></tr>
        <tr><th>Total</th><td>0</td></tr>
        </table>
        HTML;

        $detailView = DetailView::widget()
            ->emptyValue(' - ')
            ->attributes(
                [
                    ['attribute' => 'id'],
                    ['attribute' => 'username'],
                    ['attribute' => 'total:number'],
                ],
            )
            ->model(['id' => 1, 'username' => '', 'total' => '0']);
        $this->assertEqualsWithoutLE($html, $detailView->render());
    }
}
