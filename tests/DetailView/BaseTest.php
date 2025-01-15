<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\DetailView;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\Field\DataField;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class BaseTest extends TestCase
{
    use TestTrait;

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div class="test-class">
            <dl>
            <div>
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd>tests 1</dd>
            </div>
            <div>
            <dt>total</dt>
            <dd>10</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->properties(['class' => 'test-class'])
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('total'),
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testContainerAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <dl class="test-class">
            <div>
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd>tests 1</dd>
            </div>
            <div>
            <dt>total</dt>
            <dd>10</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('total'),
                )
                ->containerProperties(['class' => 'test-class'])
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testDataAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <dl>
            <div class="test-class">
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div class="test-class">
            <dt>username</dt>
            <dd>tests 1</dd>
            </div>
            <div class="test-class">
            <dt>total</dt>
            <dd>10</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('total'),
                )
                ->dataAttributes(['class' => 'test-class'])
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testHeader(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            Test header
            <dl>
            <div>
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd>tests 1</dd>
            </div>
            <div>
            <dt>total</dt>
            <dd>10</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('total'),
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->header('Test header')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testLabelAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <dl>
            <div>
            <dt class="test-label">id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt class="test-label">username</dt>
            <dd>tests 1</dd>
            </div>
            <div>
            <dt class="test-label">total</dt>
            <dd>10</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('total'),
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->labelAttributes(['class' => 'test-label'])
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testLabelTag(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <dl>
            <div>
            <p>id</p>
            <dd>1</dd>
            </div>
            <div>
            <p>username</p>
            <dd>tests 1</dd>
            </div>
            <div>
            <p>total</p>
            <dd>10</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('total'),
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->labelTag('p')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testRender(): void
    {
        $this->assertEmpty(DetailView::widget()->render());
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testValueAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <dl>
            <div>
            <dt>id</dt>
            <dd class="test-value">1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd class="test-value">tests 1</dd>
            </div>
            <div>
            <dt>total</dt>
            <dd class="test-value">10</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('total'),
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
                ->valueProperties(['class' => 'test-value'])
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testValueFalse(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <dl>
            <div>
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd>guess</dd>
            </div>
            <div>
            <dt>isAdmin</dt>
            <dd>no</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('isAdmin'),
                )
                ->data(['id' => 1, 'username' => 'guess', 'isAdmin' => false])
                ->valueFalse('no')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testValueTrue(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <dl>
            <div>
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd>admin</dd>
            </div>
            <div>
            <dt>isAdmin</dt>
            <dd>yes</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('isAdmin'),
                )
                ->data(['id' => 1, 'username' => 'admin', 'isAdmin' => true])
                ->valueTrue('yes')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws CircularReferenceException
     * @throws NotFoundException
     * @throws NotInstantiableException
     */
    public function testValueWithDataArray(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <dl>
            <div>
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd>tests 1</dd>
            </div>
            <div>
            <dt>total</dt>
            <dd>0</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('total'),
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => 0])
                ->valueTrue('yes')
                ->render(),
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <dl>
            <div>
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd>tests 1</dd>
            </div>
            <div>
            <dt>status</dt>
            <dd>false</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('status'),
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'status' => false])
                ->render(),
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <dl>
            <div>
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd>tests 1</dd>
            </div>
            <div>
            <dt>status</dt>
            <dd>true</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('status'),
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'status' => true])
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws CircularReferenceException
     * @throws NotFoundException
     * @throws NotInstantiableException
     */
    public function testValueWithDataObject(): void
    {
        $dataObject = new stdClass();

        $dataObject->id = 1;
        $dataObject->username = 'tests 1';
        $dataObject->total = 0;

        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <dl>
            <div>
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd>tests 1</dd>
            </div>
            <div>
            <dt>total</dt>
            <dd>0</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('total'),
                )
                ->data($dataObject)
                ->valueTrue('yes')
                ->render(),
        );

        $dataObject = new stdClass();

        $dataObject->id = 1;
        $dataObject->username = 'tests 1';
        $dataObject->status = false;

        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <dl>
            <div>
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd>tests 1</dd>
            </div>
            <div>
            <dt>status</dt>
            <dd>false</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('status'),
                )
                ->data($dataObject)
                ->render(),
        );

        $dataObject = new stdClass();

        $dataObject->id = 1;
        $dataObject->username = 'tests 1';
        $dataObject->status = true;

        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <dl>
            <div>
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd>tests 1</dd>
            </div>
            <div>
            <dt>status</dt>
            <dd>true</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('status'),
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'status' => true])
                ->render(),
        );
    }
}
