<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\DetailView;

use StdClass;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class BaseTest extends TestCase
{
    use TestTrait;

    public function testAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div class="test-class">
            <div>
            <div>
            <span>id</span>
            <div>1</div>
            </div>
            <div>
            <span>username</span>
            <div>tests 1</div>
            </div>
            <div>
            <span>total</span>
            <div>10</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->attributes(['class' => 'test-class'])
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'total'],
                    ],
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->render(),
        );
    }

    public function testContainerAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div class="test-class">
            <div>
            <span>id</span>
            <div>1</div>
            </div>
            <div>
            <span>username</span>
            <div>tests 1</div>
            </div>
            <div>
            <span>total</span>
            <div>10</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'total'],
                    ],
                )
                ->containerAttributes(['class' => 'test-class'])
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->render(),
        );
    }

    public function testDataAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div class="test-class">
            <span>id</span>
            <div>1</div>
            </div>
            <div class="test-class">
            <span>username</span>
            <div>tests 1</div>
            </div>
            <div class="test-class">
            <span>total</span>
            <div>10</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'total'],
                    ],
                )
                ->dataAttributes(['class' => 'test-class'])
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->render(),
        );
    }

    public function testHeader(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            Test header
            <div>
            <span>id</span>
            <div>1</div>
            </div>
            <div>
            <span>username</span>
            <div>tests 1</div>
            </div>
            <div>
            <span>total</span>
            <div>10</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'total'],
                    ],
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->header('Test header')
                ->render(),
        );
    }

    public function testLabelAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <span class="test-label">id</span>
            <div>1</div>
            </div>
            <div>
            <span class="test-label">username</span>
            <div>tests 1</div>
            </div>
            <div>
            <span class="test-label">total</span>
            <div>10</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'total'],
                    ],
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->labelAttributes(['class' => 'test-label'])
                ->render(),
        );
    }

    public function testLabelTag(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <p>id</p>
            <div>1</div>
            </div>
            <div>
            <p>username</p>
            <div>tests 1</div>
            </div>
            <div>
            <p>total</p>
            <div>10</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'total'],
                    ],
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->labelTag('p')
                ->render(),
        );
    }

    public function testRender(): void
    {
        $this->assertEmpty(DetailView::widget()->columns([])->render());
    }

    public function testValueAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <span>id</span>
            <div class="test-value">1</div>
            </div>
            <div>
            <span>username</span>
            <div class="test-value">tests 1</div>
            </div>
            <div>
            <span>total</span>
            <div class="test-value">10</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'total'],
                    ],
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->valueAttributes(['class' => 'test-value'])
                ->render(),
        );
    }

    public function testValueFalse(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <span>id</span>
            <div>1</div>
            </div>
            <div>
            <span>username</span>
            <div>guess</div>
            </div>
            <div>
            <span>isAdmin</span>
            <div>no</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'isAdmin'],
                    ],
                )
                ->data(['id' => 1, 'username' => 'guess', 'isAdmin' => false])
                ->valueFalse('no')
                ->render(),
        );
    }

    public function testValueTrue(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <span>id</span>
            <div>1</div>
            </div>
            <div>
            <span>username</span>
            <div>admin</div>
            </div>
            <div>
            <span>isAdmin</span>
            <div>yes</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'isAdmin'],
                    ],
                )
                ->data(['id' => 1, 'username' => 'admin', 'isAdmin' => true])
                ->valueTrue('yes')
                ->render(),
        );
    }

    public function testValueWithDataArray(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <span>id</span>
            <div>1</div>
            </div>
            <div>
            <span>username</span>
            <div>tests 1</div>
            </div>
            <div>
            <span>total</span>
            <div>0</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'total'],
                    ],
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => 0])
                ->valueTrue('yes')
                ->render(),
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <span>id</span>
            <div>1</div>
            </div>
            <div>
            <span>username</span>
            <div>tests 1</div>
            </div>
            <div>
            <span>status</span>
            <div>false</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'status'],
                    ],
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'status' => false])
                ->render(),
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <span>id</span>
            <div>1</div>
            </div>
            <div>
            <span>username</span>
            <div>tests 1</div>
            </div>
            <div>
            <span>status</span>
            <div>true</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'status'],
                    ],
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'status' => true])
                ->render(),
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <span>id</span>
            <div>1</div>
            </div>
            <div>
            <span>username</span>
            <div>tests 1</div>
            </div>
            <div>
            <span>status</span>
            <div>yes</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        [
                            'attribute' => 'status',
                            'value' => static fn (array $data): string => $data['status'] ? 'yes' : 'no',
                        ],
                    ],
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'status' => true])
                ->render(),
        );
    }

    public function testValueWithDataObject(): void
    {
        $dataObject = new StdClass();

        $dataObject->id = 1;
        $dataObject->username = 'tests 1';
        $dataObject->total = 0;

        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <span>id</span>
            <div>1</div>
            </div>
            <div>
            <span>username</span>
            <div>tests 1</div>
            </div>
            <div>
            <span>total</span>
            <div>0</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'total'],
                    ],
                )
                ->data($dataObject)
                ->valueTrue('yes')
                ->render(),
        );

        $dataObject = new StdClass();

        $dataObject->id = 1;
        $dataObject->username = 'tests 1';
        $dataObject->status = false;

        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <span>id</span>
            <div>1</div>
            </div>
            <div>
            <span>username</span>
            <div>tests 1</div>
            </div>
            <div>
            <span>status</span>
            <div>false</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'status'],
                    ],
                )
                ->data($dataObject)
                ->render(),
        );

        $dataObject = new StdClass();

        $dataObject->id = 1;
        $dataObject->username = 'tests 1';
        $dataObject->status = true;

        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <span>id</span>
            <div>1</div>
            </div>
            <div>
            <span>username</span>
            <div>tests 1</div>
            </div>
            <div>
            <span>status</span>
            <div>true</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        ['attribute' => 'status'],
                    ],
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'status' => true])
                ->render(),
        );

        $dataObject = new StdClass();

        $dataObject->id = 1;
        $dataObject->username = 'tests 1';
        $dataObject->status = true;

        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <span>id</span>
            <div>1</div>
            </div>
            <div>
            <span>username</span>
            <div>tests 1</div>
            </div>
            <div>
            <span>status</span>
            <div>yes</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->columns(
                    [
                        ['attribute' => 'id'],
                        ['attribute' => 'username'],
                        [
                            'attribute' => 'status',
                            'value' => static fn (object $data): string => $data->status ? 'yes' : 'no',
                        ],
                    ],
                )
                ->data($dataObject)
                ->render(),
        );
    }
}
