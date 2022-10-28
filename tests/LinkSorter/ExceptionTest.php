<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\LinkSorter;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\Exception\UrlGeneratorNotSetException;
use Yiisoft\Yii\DataView\LinkSorter;

final class ExceptionTest extends TestCase
{
    /**
     * @throws InvalidConfigException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container(ContainerConfig::create());
        WidgetFactory::initialize($container, []);
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testUrlGeneratorNotSet(): void
    {
        $this->expectException(UrlGeneratorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "urlgenerator" is not set.');

        LinkSorter::widget()
            ->attribute('username')
            ->attributes([
                'username' => [
                    'asc' => ['username' => SORT_ASC],
                    'desc' => ['username' => SORT_DESC],
                    'label' => 'Username',
                ],
            ])
            ->render();
    }
}
