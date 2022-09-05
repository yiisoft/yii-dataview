<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests;

use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Widget\WidgetFactory;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $container = new Container(ContainerConfig::create());
        WidgetFactory::initialize($container, []);
    }
}
