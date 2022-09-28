<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Router\Route;
use Yiisoft\Router\UrlGeneratorInterface;

trait TestTrait
{
    private function createIterableProvider(array $data): IterableDataReader
    {
        return new IterableDataReader($data);
    }

    private function createOffsetPaginator(
        array $data,
        int $pageSize,
        bool $sort = false
    ): OffSetPaginator {
        $data = $this->createIterableProvider($data);

        if ($sort) {
            $data = $data->withSort(Sort::any()->withOrder(['id' => 'asc', 'name' => 'asc']));
        }

        return (new OffsetPaginator($data))->withPageSize($pageSize);
    }

    private function createKeysetPaginator(array $data, int $pageSize): KeySetPaginator
    {
        $data = $this
            ->createIterableProvider($data)
            ->withSort(Sort::any()->withOrder(['id' => 'asc', 'name' => 'asc']));

        return (new KeysetPaginator($data))->withPageSize($pageSize);
    }
}
