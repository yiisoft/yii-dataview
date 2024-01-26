<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\Exception\PaginatorNotSetException;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\KeysetPagination;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class KeysetPaginationTest extends TestCase
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
            <table>
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
                ->id('w1-grid')
                ->dataReader($keysetPaginator)
                ->pagination(KeysetPagination::widget()->paginator($keysetPaginator)->render())
                ->render(),
        );
    }

    public function testRenderPaginationLinks(): void
    {
        $keysetPaginator = $this->createKeysetPaginator($this->data, 5);

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>name1</td>
            <td>description1</td>
            </tr>
            <tr>
            <td>2</td>
            <td>name2</td>
            <td>description2</td>
            </tr>
            <tr>
            <td>3</td>
            <td>name3</td>
            <td>description3</td>
            </tr>
            <tr>
            <td>4</td>
            <td>name4</td>
            <td>description4</td>
            </tr>
            <tr>
            <td>5</td>
            <td>name5</td>
            <td>description5</td>
            </tr>
            </tbody>
            </table>
            <nav>
            <a>Previous</a>
            <a href="#5">Next</a>
            </nav>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('description'),
                )
                ->id('w1-grid')
                ->dataReader($keysetPaginator)
                ->pagination(KeysetPagination::widget()->paginator($keysetPaginator)->render())
                ->layout('{items}' . PHP_EOL . '{pager}')
                ->render(),
        );

        $keysetPaginator = $keysetPaginator->withToken(PageToken::next('5'));

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>6</td>
            <td>name6</td>
            <td>description6</td>
            </tr>
            <tr>
            <td>7</td>
            <td>name7</td>
            <td>description7</td>
            </tr>
            <tr>
            <td>8</td>
            <td>name8</td>
            <td>description8</td>
            </tr>
            <tr>
            <td>9</td>
            <td>name9</td>
            <td>description9</td>
            </tr>
            <tr>
            <td>10</td>
            <td>name10</td>
            <td>description10</td>
            </tr>
            </tbody>
            </table>
            <nav>
            <a href="#6">Previous</a>
            <a href="#10">Next</a>
            </nav>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('description'),
                )
                ->id('w1-grid')
                ->dataReader($keysetPaginator)
                ->pagination(KeysetPagination::widget()->paginator($keysetPaginator)->render())
                ->layout('{items}' . PHP_EOL . '{pager}')
                ->render(),
        );

        $keysetPaginator = $keysetPaginator->withToken(PageToken::next('10'));

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>11</td>
            <td>name11</td>
            <td>description11</td>
            </tr>
            <tr>
            <td>12</td>
            <td>name12</td>
            <td>description12</td>
            </tr>
            </tbody>
            </table>
            <nav>
            <a href="#11">Previous</a>
            <a>Next</a>
            </nav>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('description'),
                )
                ->id('w1-grid')
                ->dataReader($keysetPaginator)
                ->pagination(KeysetPagination::widget()->paginator($keysetPaginator)->render())
                ->layout('{items}' . PHP_EOL . '{pager}')
                ->render(),
        );

        $keysetPaginator = $keysetPaginator->withToken(PageToken::next('5'));

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>6</td>
            <td>name6</td>
            <td>description6</td>
            </tr>
            <tr>
            <td>7</td>
            <td>name7</td>
            <td>description7</td>
            </tr>
            <tr>
            <td>8</td>
            <td>name8</td>
            <td>description8</td>
            </tr>
            <tr>
            <td>9</td>
            <td>name9</td>
            <td>description9</td>
            </tr>
            <tr>
            <td>10</td>
            <td>name10</td>
            <td>description10</td>
            </tr>
            </tbody>
            </table>
            <nav>
            <a href="#6">Previous</a>
            <a href="#10">Next</a>
            </nav>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('description'),
                )
                ->id('w1-grid')
                ->dataReader($keysetPaginator)
                ->pagination(KeysetPagination::widget()->paginator($keysetPaginator)->render())
                ->layout('{items}' . PHP_EOL . '{pager}')
                ->render(),
        );

        $keysetPaginator = $keysetPaginator->withToken(PageToken::next('0'));

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>name1</td>
            <td>description1</td>
            </tr>
            <tr>
            <td>2</td>
            <td>name2</td>
            <td>description2</td>
            </tr>
            <tr>
            <td>3</td>
            <td>name3</td>
            <td>description3</td>
            </tr>
            <tr>
            <td>4</td>
            <td>name4</td>
            <td>description4</td>
            </tr>
            <tr>
            <td>5</td>
            <td>name5</td>
            <td>description5</td>
            </tr>
            </tbody>
            </table>
            <nav>
            <a>Previous</a>
            <a href="#5">Next</a>
            </nav>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('description'),
                )
                ->id('w1-grid')
                ->dataReader($keysetPaginator)
                ->pagination(KeysetPagination::widget()->paginator($keysetPaginator)->render())
                ->layout('{items}' . PHP_EOL . '{pager}')
                ->render(),
        );
    }

    public function testNotSetPaginator(): void
    {
        $pagination = KeysetPagination::widget();

        $this->expectException(PaginatorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "paginator" is not set.');
        Assert::invokeMethod($pagination, 'getPaginator');
    }
}
