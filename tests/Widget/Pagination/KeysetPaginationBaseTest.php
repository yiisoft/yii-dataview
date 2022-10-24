<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\Pagination;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\Widget\KeysetPagination;

final class KeysetPaginationBaseTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'name1', 'description' => 'description1'],
        ['id' => 2, 'name' => 'name2', 'description' => 'description2'],
        ['id' => 3, 'name' => 'name3', 'description' => 'description3'],
        ['id' => 4, 'name' => 'name4', 'description' => 'description4'],
        ['id' => 5, 'name' => 'name5', 'description' => 'description5'],
        ['id' => 6, 'name' => 'name6', 'description' => 'description6'],
        ['id' => 7, 'name' => 'name7', 'description' => 'description7'],
        ['id' => 8, 'name' => 'name8', 'description' => 'description8'],
        ['id' => 9, 'name' => 'name9', 'description' => 'description9'],
        ['id' => 10, 'name' => 'name10', 'description' => 'description10'],
        ['id' => 11, 'name' => 'name11', 'description' => 'description11'],
        ['id' => 12, 'name' => 'name12', 'description' => 'description12'],
    ];

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testRenderPaginatorEmptyData(): void
    {
        $keysetPaginator = $this->createKeysetPaginator([], 10);

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr></tr>
            </thead>
            <tbody>
            <tr>
            <td colspan="0">No results found.</td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            GridView::widget()
                ->columns([])
                ->id('w1-grid')
                ->paginator($keysetPaginator)
                ->pagination(KeysetPagination::widget()->paginator($keysetPaginator)->render())
                ->translator(Mock::translator('en'))
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testRenderPaginationLinks(): void
    {
        $keysetPaginator = $this->createKeysetPaginator($this->data, 5);

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th><a class="asc" href="/admin/manage?page=0&amp;pagesize=5&amp;sort=-id%2Cname" data-sort="-id,name">Id <i class="bi bi-sort-alpha-up"></i></a></th>
            <th><a class="asc" href="/admin/manage?page=0&amp;pagesize=5&amp;sort=-name%2Cid" data-sort="-name,id">Name <i class="bi bi-sort-alpha-up"></i></a></th>
            <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">name1</td>
            <td data-label="description">description1</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">name2</td>
            <td data-label="description">description2</td>
            </tr>
            <tr>
            <td data-label="id">3</td>
            <td data-label="name">name3</td>
            <td data-label="description">description3</td>
            </tr>
            <tr>
            <td data-label="id">4</td>
            <td data-label="name">name4</td>
            <td data-label="description">description4</td>
            </tr>
            <tr>
            <td data-label="id">5</td>
            <td data-label="name">name5</td>
            <td data-label="description">description5</td>
            </tr>
            </tbody>
            </table>
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=0&amp;pagesize=5">Previous</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=5">Next Page</a></li>
            </ul>
            </nav>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($keysetPaginator)
                ->pagination(KeysetPagination::widget()->paginator($keysetPaginator)->urlArguments([])->render())
                ->layoutGridTable('{items}' . PHP_EOL . '{pager}')
                ->translator(Mock::translator('en'))
                ->render(),
        );

        $keysetPaginator = $keysetPaginator->withNextPageToken('5');

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th><a class="asc" href="/admin/manage?page=0&amp;pagesize=5&amp;sort=-id%2Cname" data-sort="-id,name">Id <i class="bi bi-sort-alpha-up"></i></a></th>
            <th><a class="asc" href="/admin/manage?page=0&amp;pagesize=5&amp;sort=-name%2Cid" data-sort="-name,id">Name <i class="bi bi-sort-alpha-up"></i></a></th>
            <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">6</td>
            <td data-label="name">name6</td>
            <td data-label="description">description6</td>
            </tr>
            <tr>
            <td data-label="id">7</td>
            <td data-label="name">name7</td>
            <td data-label="description">description7</td>
            </tr>
            <tr>
            <td data-label="id">8</td>
            <td data-label="name">name8</td>
            <td data-label="description">description8</td>
            </tr>
            <tr>
            <td data-label="id">9</td>
            <td data-label="name">name9</td>
            <td data-label="description">description9</td>
            </tr>
            <tr>
            <td data-label="id">10</td>
            <td data-label="name">name10</td>
            <td data-label="description">description10</td>
            </tr>
            </tbody>
            </table>
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link" href="/admin/manage?page=0&amp;pagesize=5">Previous</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=10&amp;pagesize=5">Next Page</a></li>
            </ul>
            </nav>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($keysetPaginator)
                ->pagination(KeysetPagination::widget()->paginator($keysetPaginator)->urlArguments([])->render())
                ->layoutGridTable('{items}' . PHP_EOL . '{pager}')
                ->translator(Mock::translator('en'))
                ->render(),
        );

        $keysetPaginator = $keysetPaginator->withNextPageToken('10');

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th><a class="asc" href="/admin/manage?page=0&amp;pagesize=5&amp;sort=-id%2Cname" data-sort="-id,name">Id <i class="bi bi-sort-alpha-up"></i></a></th>
            <th><a class="asc" href="/admin/manage?page=0&amp;pagesize=5&amp;sort=-name%2Cid" data-sort="-name,id">Name <i class="bi bi-sort-alpha-up"></i></a></th>
            <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">11</td>
            <td data-label="name">name11</td>
            <td data-label="description">description11</td>
            </tr>
            <tr>
            <td data-label="id">12</td>
            <td data-label="name">name12</td>
            <td data-label="description">description12</td>
            </tr>
            </tbody>
            </table>
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=5">Previous</a></li>
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=0&amp;pagesize=5">Next Page</a></li>
            </ul>
            </nav>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($keysetPaginator)
                ->pagination(KeysetPagination::widget()->paginator($keysetPaginator)->urlArguments([])->render())
                ->layoutGridTable('{items}' . PHP_EOL . '{pager}')
                ->translator(Mock::translator('en'))
                ->render(),
        );

        $keysetPaginator = $keysetPaginator->withNextPageToken('5');

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th><a class="asc" href="/admin/manage?page=0&amp;pagesize=5&amp;sort=-id%2Cname" data-sort="-id,name">Id <i class="bi bi-sort-alpha-up"></i></a></th>
            <th><a class="asc" href="/admin/manage?page=0&amp;pagesize=5&amp;sort=-name%2Cid" data-sort="-name,id">Name <i class="bi bi-sort-alpha-up"></i></a></th>
            <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">6</td>
            <td data-label="name">name6</td>
            <td data-label="description">description6</td>
            </tr>
            <tr>
            <td data-label="id">7</td>
            <td data-label="name">name7</td>
            <td data-label="description">description7</td>
            </tr>
            <tr>
            <td data-label="id">8</td>
            <td data-label="name">name8</td>
            <td data-label="description">description8</td>
            </tr>
            <tr>
            <td data-label="id">9</td>
            <td data-label="name">name9</td>
            <td data-label="description">description9</td>
            </tr>
            <tr>
            <td data-label="id">10</td>
            <td data-label="name">name10</td>
            <td data-label="description">description10</td>
            </tr>
            </tbody>
            </table>
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link" href="/admin/manage?page=0&amp;pagesize=5">Previous</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=10&amp;pagesize=5">Next Page</a></li>
            </ul>
            </nav>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($keysetPaginator)
                ->pagination(KeysetPagination::widget()->paginator($keysetPaginator)->urlArguments([])->render())
                ->layoutGridTable('{items}' . PHP_EOL . '{pager}')
                ->translator(Mock::translator('en'))
                ->render(),
        );

        $keysetPaginator = $keysetPaginator->withNextPageToken('0');

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th><a class="asc" href="/admin/manage?page=0&amp;pagesize=5&amp;sort=-id%2Cname" data-sort="-id,name">Id <i class="bi bi-sort-alpha-up"></i></a></th>
            <th><a class="asc" href="/admin/manage?page=0&amp;pagesize=5&amp;sort=-name%2Cid" data-sort="-name,id">Name <i class="bi bi-sort-alpha-up"></i></a></th>
            <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">name1</td>
            <td data-label="description">description1</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">name2</td>
            <td data-label="description">description2</td>
            </tr>
            <tr>
            <td data-label="id">3</td>
            <td data-label="name">name3</td>
            <td data-label="description">description3</td>
            </tr>
            <tr>
            <td data-label="id">4</td>
            <td data-label="name">name4</td>
            <td data-label="description">description4</td>
            </tr>
            <tr>
            <td data-label="id">5</td>
            <td data-label="name">name5</td>
            <td data-label="description">description5</td>
            </tr>
            </tbody>
            </table>
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=0&amp;pagesize=5">Previous</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=5">Next Page</a></li>
            </ul>
            </nav>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($keysetPaginator)
                ->pagination(KeysetPagination::widget()->paginator($keysetPaginator)->urlArguments([])->render())
                ->layoutGridTable('{items}' . PHP_EOL . '{pager}')
                ->translator(Mock::translator('en'))
                ->render(),
        );
    }

    /**
     * @psalm-return array<DataColumn>
     */
    private function createColumns(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            DataColumn::create()->attribute('description'),
        ];
    }
}
