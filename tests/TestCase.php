<?php

namespace Yiisoft\Yii\DataView\Tests;

use hiqdev\composer\config\Builder;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Di\Container;
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
        $assets = $container->get(Aliases::class);
        $container->get(ListView::class);
        $container->get(DetailView::class);
        $container->get(ActionColumn::class);
    }
}
