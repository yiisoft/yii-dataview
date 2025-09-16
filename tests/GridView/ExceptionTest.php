<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\Exception\DataReaderNotSetException;
use Yiisoft\Yii\DataView\GridView;

final class ExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        $container = new Container(ContainerConfig::create());
        WidgetFactory::initialize($container, []);
    }

    public function testRenderWithoutDataReader(): void
    {
        $this->expectException(DataReaderNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "dataReader" is not set.');
        GridView::widget()->render();
    }

    public function testPaginator(): void
    {
        $this->expectException(DataReaderNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "dataReader" is not set.');
        GridView::widget()->render();
    }
}
