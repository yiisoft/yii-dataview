<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Reference;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\Route;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\Column\ActionColumnRenderer;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\YiiRouter\ActionColumnUrlCreator;

trait TestTrait
{
    /**
     * @throws InvalidConfigException
     */
    protected function setUp(): void
    {
        $container = new Container(ContainerConfig::create()->withDefinitions($this->config()));
        WidgetFactory::initialize($container, [
            GridView::class => [
                'addRendererConstructorArguments()' => [
                    [
                        ActionColumnRenderer::class => [
                            'defaultUrlCreator' => Reference::to(ActionColumnUrlCreator::class),
                        ],
                    ]
                ],
            ],
        ]);
    }

    private function createOffsetPaginator(
        array $data,
        int $pageSize,
        int $currentPage = 1,
        bool $sort = false
    ): OffSetPaginator {
        $data = new IterableDataReader($data);

        if ($sort) {
            $data = $data->withSort(Sort::any()->withOrder(['id' => 'asc', 'name' => 'asc']));
        }

        return (new OffsetPaginator($data))->withToken(PageToken::next((string) $currentPage))->withPageSize($pageSize);
    }

    private function createKeysetPaginator(array $data, int $pageSize): KeySetPaginator
    {
        $data = (new IterableDataReader($data))
            ->withSort(Sort::any()->withOrder(['id' => 'asc', 'name' => 'asc']));

        return (new KeysetPaginator($data))->withPageSize($pageSize);
    }

    private function config(): array
    {
        $currentRoute = new CurrentRoute();
        $currentRoute->setRouteWithArguments(Route::get('/admin/manage')->name('admin/manage'), []);

        return [
            CurrentRoute::class => $currentRoute,
            UrlGeneratorInterface::class => Mock::urlGenerator([], $currentRoute),
        ];
    }
}
