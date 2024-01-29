<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\OffsetPagination;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\SimplePaginationUrlCreator;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\UrlConfig;

final class BaseTest extends TestCase
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
    public function testRenderWithUrlQueryParametersWithoutUrlName(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a href="/route?filter=test&amp;pagesize=1">⟪</a>
            <a href="/route?filter=test&amp;pagesize=1">⟨</a>
            <a href="/route?filter=test&amp;pagesize=1">1</a>
            <a href="/route?filter=test&amp;page=2&amp;pagesize=1">2</a>
            <a href="/route?filter=test&amp;page=3&amp;pagesize=1">3</a>
            <a href="/route?filter=test&amp;page=4&amp;pagesize=1">4</a>
            <a href="/route?filter=test&amp;page=5&amp;pagesize=1">5</a>
            <a href="/route?filter=test&amp;page=2&amp;pagesize=1">⟩</a>
            <a href="/route?filter=test&amp;page=5&amp;pagesize=1">⟫</a>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->paginator($this->createOffsetPaginator($this->data, 1))
                ->urlCreator(new SimplePaginationUrlCreator())
                ->urlConfig(new UrlConfig(queryParameters: ['filter' => 'test']))
                ->render(),
        );
    }
}
