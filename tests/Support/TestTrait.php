<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Widget\WidgetFactory;

trait TestTrait
{
    /**
     * @throws InvalidConfigException
     */
    protected function setUp(): void
    {
        $container = new Container(ContainerConfig::create()->withDefinitions($this->config()));
        WidgetFactory::initialize($container, []);
    }

    private function createIterableProvider(array $data): IterableDataReader
    {
        return new IterableDataReader($data);
    }

    private function createOffsetPaginator(
        array $data,
        int $pageSize,
        int $currentPage = 1,
        bool $sort = false
    ): OffSetPaginator {
        $data = $this->createIterableProvider($data);

        if ($sort) {
            $data = $data->withSort(Sort::any()->withOrder(['id' => 'asc', 'name' => 'asc']));
        }

        return (new OffsetPaginator($data))->withNextPageToken((string) $currentPage)->withPageSize($pageSize);
    }

    private function createKeysetPaginator(array $data, int $pageSize): KeySetPaginator
    {
        $data = $this
            ->createIterableProvider($data)
            ->withSort(Sort::any()->withOrder(['id' => 'asc', 'name' => 'asc']));

        return (new KeysetPaginator($data))->withPageSize($pageSize);
    }

    private function config(): array
    {
        return [
            UrlGeneratorInterface::class => Mock::urlGenerator(),
        ];
    }
}
