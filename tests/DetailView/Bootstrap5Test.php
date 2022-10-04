<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\DetailView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Html\Tag\H2;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class Bootstrap5Test extends TestCase
{
    use TestTrait;

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
                    [
                        [
                            'attribute' => 'id',
                            'label' => 'Id',
                        ],
                        [
                            'attribute' => 'login',
                            'label' => 'login',
                        ],
                        [
                            'attribute' => 'created_at',
                            'label' => 'Created At',
                        ],
                    ],
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
                    H2::tag()->class('text-center')->content('<strong>Bootstrap 5</strong>')->encode(false)->render()
                )
                ->labelAttributes(['class' => 'fw-bold'])
                ->valueAttributes(['class' => 'alert alert-info'])
                ->render(),
        );
    }
}
