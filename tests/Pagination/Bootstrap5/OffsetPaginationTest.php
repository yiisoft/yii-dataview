<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination\Bootstrap5;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\OffsetPagination;
use Yiisoft\Yii\DataView\PageContext;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\SimplePaginationUrlCreator;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

/**
 * Documentation and examples for showing pagination to indicate a series of related content exists across multiple
 * pages.
 *
 * @link https://getbootstrap.com/docs/5.2/components/pagination/
 */
final class OffsetPaginationTest extends TestCase
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
            <a href="/route?page=1&amp;pagesize=1&amp;filter=test">First</a>
            <a href="/route?page=1&amp;pagesize=1&amp;filter=test">Previous</a>
            <a href="/route?page=1&amp;pagesize=1&amp;filter=test">1</a>
            <a href="/route?page=2&amp;pagesize=1&amp;filter=test">2</a>
            <a href="/route?page=3&amp;pagesize=1&amp;filter=test">3</a>
            <a href="/route?page=4&amp;pagesize=1&amp;filter=test">4</a>
            <a href="/route?page=5&amp;pagesize=1&amp;filter=test">5</a>
            <a href="/route?page=2&amp;pagesize=1&amp;filter=test">Next</a>
            <a href="/route?page=5&amp;pagesize=1&amp;filter=test">Last</a>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->paginator($this->createOffsetPaginator($this->data, 1))
                ->urlCreator(new SimplePaginationUrlCreator())
                ->queryParameters(['filter' => 'test'])
                ->render(),
        );
    }

    /**
     * We use a large block of connected links for our pagination, making links hard to miss and easily scalable—all
     * while providing large hit areas. Pagination is built with list HTML elements so screen readers can announce the
     * number of available links. Use a wrapping <nav> element to identify it as a navigation section to screen readers
     * and other assistive technologies.
     *
     * In addition, as pages likely have more than one such navigation section, it’s advisable to provide a descriptive
     * aria-label for the <nav> to reflect its purpose. For example, if the pagination component is used to navigate
     * between a set of search results, an appropriate label could be aria-label="Search results pages".
     *
     * @link https://getbootstrap.com/docs/5.2/components/pagination/#overview
     */
    public function testOverview(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a href="#1">First</a>
            <a href="#1">Previous</a>
            <a href="#1">1</a>
            <a href="#2">2</a>
            <a href="#3">3</a>
            <a href="#4">4</a>
            <a href="#5">5</a>
            <a href="#2">Next</a>
            <a href="#5">Last</a>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->paginator($this->createOffsetPaginator($this->data, 1))
                ->render(),
        );
    }
}
