<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\DetailView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Html\Tag\H2;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\Field\DataField;
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
            <h2 class="text-center"><strong>Bootstrap 5</strong></h2>
            <dl class="row flex-column justify-content-center align-items-center">
            <div class="col-xl-5">
            <dt class="fw-bold">Id</dt>
            <dd class="alert alert-info">1</dd>
            </div>
            <div class="col-xl-5">
            <dt class="fw-bold">login</dt>
            <dd class="alert alert-info">test</dd>
            </div>
            <div class="col-xl-5">
            <dt class="fw-bold">Created At</dt>
            <dd class="alert alert-info">2020-01-01</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->attributes(['class' => 'container'])
                ->fields(
                    new Datafield('id', label: 'Id'),
                    new Datafield('login'),
                    new Datafield('created_at', label: 'Created At'),
                )
                ->fieldListAttributes(['class' => 'row flex-column justify-content-center align-items-center'])
                ->data(
                    [
                        'id' => 1,
                        'login' => 'test',
                        'created_at' => '2020-01-01',
                    ],
                )
                ->fieldAttributes(['class' => 'col-xl-5'])
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
                ->fields(
                    new Datafield('id', label: 'Id'),
                    new Datafield('login'),
                    new Datafield('created_at', label: 'Created At'),
                )
                ->data(
                    [
                        'id' => 1,
                        'login' => 'test',
                        'created_at' => '2020-01-01',
                    ],
                )
                ->fieldAttributes(['class' => 'col-xl-5'])
                ->header(
                    H2::tag()->addClass('text-center')->content('<strong>Bootstrap 5</strong>')->encode(false)->render()
                )
                ->labelAttributes(['class' => 'fw-bold'])
                ->labelTemplate('<th{attributes}>{label}</th>')
                ->fieldTemplate("<tr>\n{label}\n{value}\n</tr>")
                ->template("<table{attributes}>\n{header}\n{fields}\n</table>")
                ->valueTemplate('<td{attributes}>{value}</td>')
                ->render(),
        );
    }
}
