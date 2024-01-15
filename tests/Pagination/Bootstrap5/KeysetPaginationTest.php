<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination\Bootstrap5;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\KeysetPagination;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\SimplePaginationUrlCreator;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

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

    public function testRenderWithUrlQueryParameters(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a>Previous</a>
            <a href="/route?page=2&amp;pagesize=2&amp;filter=test">Next</a>
            </nav>
            HTML,
            KeysetPagination::widget()
                ->paginator($this->createKeysetPaginator($this->data, 2))
                ->urlCreator(new SimplePaginationUrlCreator())
                ->queryParameters(['filter' => 'test'])
                ->render(),
        );
    }
}
