<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

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

    private function createPaginator(array $data, int $pageSize, int $currentPage, bool $sort = false): OffSetPaginator
    {
        $data = $this->createIterableProvider($data);

        if ($sort) {
            $data = $data->withSort(Sort::any()->withOrder(['id' => 'asc', 'name' => 'asc']));
        }

        return (new OffsetPaginator($data))->withPageSize($pageSize)->withCurrentPage($currentPage);
    }

    private function createUrlGenerator(): UrlGeneratorInterface
    {
        return Mock::urlGenerator(
            [
                Route::get('/admin/delete')->name('admin/delete'),
                Route::get('/admin/manage')->name('admin/manage'),
                Route::get('/admin/update')->name('admin/update'),
                Route::get('/admin/view')->name('admin/resend-password'),
                Route::get('/admin/view')->name('admin/view'),
            ],
        );
    }
}
