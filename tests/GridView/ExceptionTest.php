<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\DataReaderNotSetException;
use Yiisoft\Yii\DataView\GridView\GridView;

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
        GridView::widget()->render();
    }

    public function testPaginator(): void
    {
        $this->expectException(DataReaderNotSetException::class);
        GridView::widget()->render();
    }
}
