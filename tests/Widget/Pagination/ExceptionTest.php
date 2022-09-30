<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\Pagination;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Exception;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\Widget\BasePagination;
use Yiisoft\Yii\DataView\Widget\OffsetPagination;

final class ExceptionTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John', 'age' => 20],
        ['id' => 2, 'name' => 'Mary', 'age' => 21],
        ['id' => 3, 'name' => 'Samdark', 'age' => 35],
        ['id' => 4, 'name' => 'joe', 'age' => 41],
        ['id' => 5, 'name' => 'Alexey', 'age' => 32],
    ];

    public function testCurrentPageOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Current page must be less than or equal to total pages.');
        OffsetPagination::widget()
            ->currentPage(4)
            ->menuClass('pagination justify-content-center')
            ->paginator($this->createOffsetPaginator($this->data, 2))
            ->urlGenerator(Mock::urlGenerator())
            ->urlName('admin/manage')
            ->render();
    }

    public function testNotSetPaginator(): void
    {
        $basePagination = new class () extends BasePagination {
            protected function run(): string
            {
                return '';
            }
        };

        $this->expectException(Exception\PaginatorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "paginator" is not set.');
        Assert::invokeMethod($basePagination, 'getPaginator');
    }

    public function testNotSetUrlGenerator(): void
    {
        $this->expectException(Exception\UrlGeneratorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "urlgenerator" is not set.');
        OffsetPagination::widget()
            ->currentPage(1)
            ->menuClass('pagination justify-content-center')
            ->paginator($this->createOffsetPaginator($this->data, 2))
            ->urlName('admin/manage')
            ->render();
    }
}
