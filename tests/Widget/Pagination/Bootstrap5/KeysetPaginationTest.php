<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\Bootstrap5\Pagination\Bootstrap5;

use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Router\RouteCollectionInterface;
use Yiisoft\Router\RouteCollector;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\Tests\TestCase;
use Yiisoft\Yii\DataView\Widget\KeysetPagination;

/**
 * Documentation and examples for showing pagination to indicate a series of related content exists across multiple
 * pages.
 */
final class KeysetPaginationTest extends TestCase
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
     * Change the alignment of pagination components with flexbox utilities. For example, with
     * `.justify-content-center`.
     */
    public function testAlignment(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav aria-label="Pagination">
            <ul class="pagination justify-content-center">
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=0&amp;pagesize=2">Previous</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=2">Next Page</a></li>
            </ul>
            </nav>
            HTML,
            KeysetPagination::widget()
                ->currentPage(1)
                ->menuClass('pagination justify-content-center')
                ->paginator($this->createKeysetPaginator($this->data, 2, 1))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->urlName('admin/manage')
                ->render(),
        );
    }
}
