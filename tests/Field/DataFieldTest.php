<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Field;

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

final class DataFieldTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John', 'age' => 20],
        ['id' => 2, 'name' => 'Mary', 'age' => 21],
    ];

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
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd>admin</dd>
            </div>
            <div>
            <dt class="test-class">isAdmin</dt>
            <dd>true</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    new Datafield('id'),
                    new Datafield('username'),
                    new Datafield('isAdmin', labelAttributes: ['class' => 'test-class']),
                )
                ->data(['id' => 1, 'username' => 'admin', 'isAdmin' => true])
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
            <dt>id</dt>
            <dd>1</dd>
            </div>
            <div>
            <dt>username</dt>
            <dd>admin</dd>
            </div>
            <div>
            <p>isAdmin</p>
            <dd>true</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    new Datafield('id'),
                    new Datafield('username'),
                    new Datafield('isAdmin', labelTag: 'p'),
                )
                ->data(['id' => 1, 'username' => 'admin', 'isAdmin' => true])
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testValueAttributeWithClosure(): void
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
            <dd class="text-success">10</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    new Datafield('id'),
                    new Datafield('username'),
                    new DataField(
                        'total',
                        valueAttributes: static fn (array $data) => $data['total'] > 10
                        ? ['class' => 'text-danger'] : ['class' => 'text-success'],
                    )
                )
                ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
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
            <dt>status</dt>
            <dd>yes</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    new Datafield('id'),
                    new Datafield('username'),
                    new DataField('status', value: static fn (array $data): string => $data['status'] ? 'yes' : 'no')
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
            <dd>yes</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    new Datafield('id'),
                    new Datafield('username'),
                    new DataField('status', value: static fn (object $data): string => $data->status ? 'yes' : 'no')
                )
                ->data($dataObject)
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testValueInt(): void
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
            <dd>1</dd>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    new Datafield('id'),
                    new Datafield('username'),
                    new Datafield('isAdmin', value: 1),
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
    public function testValueTag(): void
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
            <p>true</p>
            </div>
            </dl>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    new Datafield('id'),
                    new Datafield('username'),
                    new Datafield('isAdmin', valueTag: 'p'),
                )
                ->data(['id' => 1, 'username' => 'admin', 'isAdmin' => true])
                ->render(),
        );
    }
}
