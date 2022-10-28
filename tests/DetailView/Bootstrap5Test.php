<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\DetailView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Html\Tag\H2;
use Yiisoft\Yii\DataView\Column\DetailColumn;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class Bootstrap5Test extends TestCase
{
    use TestTrait;

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testRender(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div class="container">
            <div class="row flex-column justify-content-center align-items-center">
            <h2 class="text-center"><strong>Bootstrap 5</strong></h2>
            <div class="col-xl-5">
            <span class="fw-bold">Id</span>
            <div class="alert alert-info">1</div>
            </div>
            <div class="col-xl-5">
            <span class="fw-bold">login</span>
            <div class="alert alert-info">test</div>
            </div>
            <div class="col-xl-5">
            <span class="fw-bold">Created At</span>
            <div class="alert alert-info">2020-01-01</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->attributes(['class' => 'container'])
                ->columns(
                    DetailColumn::create()->attribute('id')->label('Id'),
                    DetailColumn::create()->attribute('login'),
                    DetailColumn::create()->attribute('created_at')->label('Created At'),
                )
                ->containerAttributes(['class' => 'row flex-column justify-content-center align-items-center'])
                ->data(
                    [
                        'id' => 1,
                        'login' => 'test',
                        'created_at' => '2020-01-01',
                    ],
                )
                ->dataAttributes(['class' => 'col-xl-5'])
                ->header(
                    H2::tag()->addClass('text-center')->content('<strong>Bootstrap 5</strong>')->encode(false)->render()
                )
                ->labelAttributes(['class' => 'fw-bold'])
                ->valueAttributes(['class' => 'alert alert-info'])
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testRenderWithTable(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <table class="table table-success table-striped">
            <h2 class="text-center"><strong>Bootstrap 5</strong></h2>
            <tr>
            <th class="fw-bold">Id</th>
            <td>1</td>
            </tr>
            <tr>
            <th class="fw-bold">login</th>
            <td>test</td>
            </tr>
            <tr>
            <th class="fw-bold">Created At</th>
            <td>2020-01-01</td>
            </tr>
            </table>
            HTML,
            DetailView::widget()
                ->attributes(['class' => 'table table-success table-striped'])
                ->columns(
                    DetailColumn::create()->attribute('id')->label('Id'),
                    DetailColumn::create()->attribute('login'),
                    DetailColumn::create()->attribute('created_at')->label('Created At'),
                )
                ->data(
                    [
                        'id' => 1,
                        'login' => 'test',
                        'created_at' => '2020-01-01',
                    ],
                )
                ->dataAttributes(['class' => 'col-xl-5'])
                ->header(
                    H2::tag()->addClass('text-center')->content('<strong>Bootstrap 5</strong>')->encode(false)->render()
                )
                ->labelAttributes(['class' => 'fw-bold'])
                ->labelTemplate('<th{labelAttributes}>{label}</th>')
                ->itemTemplate("<tr>\n{label}\n{value}\n</tr>")
                ->template("<table{attributes}>\n{header}\n{items}\n</table>")
                ->valueTemplate('<td{valueAttributes}>{value}</td>')
                ->render(),
        );
    }
}
