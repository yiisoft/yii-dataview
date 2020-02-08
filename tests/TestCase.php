<?php

namespace Yiisoft\Yii\DataView\Tests;

use hiqdev\composer\config\Builder;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Di\Container;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\Columns\ActionColumn;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\ListView;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws \Yiisoft\Factory\Exceptions\CircularReferenceException
     * @throws \Yiisoft\Factory\Exceptions\InvalidConfigException
     * @throws \Yiisoft\Factory\Exceptions\NotFoundException
     * @throws \Yiisoft\Factory\Exceptions\NotInstantiableException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container(require Builder::path('tests'));
        WidgetFactory::initialize($container, []);
        $assets = $container->get(Aliases::class);
        $container->get(ListView::class);
        $container->get(DetailView::class);
        $container->get(ActionColumn::class);
    }

    /**
     * Asserting two strings equality ignoring line endings.
     * @param string $expected
     * @param string $actual
     * @param string $message
     *
     * @return void
     */
    protected function assertEqualsWithoutLE(string $expected, string $actual, string $message = ''): void
    {
        $expected = str_replace("\r\n", "\n", $expected);
        $actual = str_replace("\r\n", "\n", $actual);

        $this->assertEquals($expected, $actual, $message);
    }
}
