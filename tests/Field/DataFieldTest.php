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
            <span class="test-class">isAdmin</span>
            <div>true</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('isAdmin')->labelAttributes(['class' => 'test-class']),
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
            <p>isAdmin</p>
            <div>true</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('isAdmin')->labelTag('p'),
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
            <div class="text-success">10</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()
                        ->attribute('total')
                        ->valueAttributes(
                            static fn (array $data) => $data['total'] > 10
                                ? ['class' => 'text-danger'] : ['class' => 'text-success'],
                        ),
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
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()
                        ->attribute('status')
                        ->value(static fn (array $data): string => $data['status'] ? 'yes' : 'no'),
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
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()
                        ->attribute('status')
                        ->value(static fn (object $data): string => $data->status ? 'yes' : 'no'),
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
            <div>1</div>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('isAdmin')->value(1),
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
            <p>true</p>
            </div>
            </div>
            </div>
            HTML,
            DetailView::widget()
                ->fields(
                    DataField::create()->attribute('id'),
                    DataField::create()->attribute('username'),
                    DataField::create()->attribute('isAdmin')->valueTag('p'),
                )
                ->data(['id' => 1, 'username' => 'admin', 'isAdmin' => true])
                ->render(),
        );
    }
}
