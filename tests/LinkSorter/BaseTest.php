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
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\LinkSorter;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class BaseTest extends TestCase
{
    use TestTrait;

    /**
     * @throws InvalidConfigException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container(ContainerConfig::create());
        WidgetFactory::initialize($container, [UrlGeneratorInterface::class => Mock::urlGenerator()]);
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testUrlQueryParametersWithUrlArgumentsFalse(): void
    {
        $this->assertSame(
            <<<HTML
            <a class="asc" href="?test=test&amp;page=1&amp;pagesize=5&amp;sort=-id%2C-username" data-sort="-id,-username">Id <i class="bi bi-sort-alpha-up"></i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('id')
                ->attributes(['id' => SORT_DESC, 'username' => SORT_ASC])
                ->currentPage(1)
                ->directions(['id' => 'asc', 'username' => 'desc'])
                ->iconAscClass('bi bi-sort-alpha-up')
                ->pageSize(5)
                ->urlQueryParameters(['test' => 'test'])
                ->render(),
        );
    }
}
