<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView;
use Yiisoft\Yii\DataView\Exception\DataReaderNotSetException;

final class ExceptionTest extends TestCase
{
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
    public function testGetPaginator(): void
    {
        $this->expectException(DataReaderNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "dataReader" is not set.');
        DataView\GridView::widget()->getDataReader();
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testPaginator(): void
    {
        $this->expectException(DataReaderNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "dataReader" is not set.');
        DataView\GridView::widget()->render();
    }
}
