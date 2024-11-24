<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ExceptionTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John', 'age' => 20],
        ['id' => 2, 'name' => 'Mary', 'age' => 21],
        ['id' => 3, 'name' => 'Samdark', 'age' => 35],
        ['id' => 4, 'name' => 'joe', 'age' => 41],
        ['id' => 5, 'name' => 'Alexey', 'age' => 32],
    ];

    /**
     * @throws InvalidConfigException
     */
    protected function setUp(): void
    {
        $container = new Container(ContainerConfig::create());
        WidgetFactory::initialize($container, []);
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testCurrentPageOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Current page must be less than or equal to total pages.');
        OffsetPagination::widget()->paginator($this->createOffsetPaginator($this->data, 2, 4))->render();
    }
}
