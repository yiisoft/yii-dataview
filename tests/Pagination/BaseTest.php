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
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link disabled" href="/route?page=1&amp;pagesize=1&amp;filter=test">Previous</a></li>
            <li class="page-item"><a class="page-link active" href="/route?page=1&amp;pagesize=1&amp;filter=test" aria-current="page">1</a></li>
            <li class="page-item"><a class="page-link" href="/route?page=2&amp;pagesize=1&amp;filter=test">2</a></li>
            <li class="page-item"><a class="page-link" href="/route?page=3&amp;pagesize=1&amp;filter=test">3</a></li>
            <li class="page-item"><a class="page-link" href="/route?page=4&amp;pagesize=1&amp;filter=test">4</a></li>
            <li class="page-item"><a class="page-link" href="/route?page=5&amp;pagesize=1&amp;filter=test">5</a></li>
            <li class="page-item"><a class="page-link" href="/route?page=2&amp;pagesize=1&amp;filter=test">Next Page</a></li>
            </ul>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->paginator($this->createOffsetPaginator($this->data, 1))
                ->urlCreator(new SimplePaginationUrlCreator())
                ->queryParameters(['filter' => 'test'])
                ->render(),
        );
    }
}
