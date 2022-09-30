<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\Bootstrap5\Pagination\Bootstrap5;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\Widget\OffsetPagination;

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

    /**
     * Change the alignment of pagination components with flexbox utilities. For example, with
     * `.justify-content-center`.
     *
     * @link https://getbootstrap.com/docs/5.2/components/pagination/#alignment
     */
    public function testAlignment(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav aria-label="Pagination">
            <ul class="pagination justify-content-center">
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=1&amp;pagesize=2">Previous</a></li>
            <li class="page-item"><a class="page-link active" href="/admin/manage?page=1&amp;pagesize=2" aria-current="page">1</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=2">2</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=3&amp;pagesize=2">3</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=2">Next Page</a></li>
            </ul>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->currentPage(1)
                ->menuClass('pagination justify-content-center')
                ->paginator($this->createOffsetPaginator($this->data, 2))
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * Pagination links are customizable for different circumstances. Use `.disabled` for links that appear un-clickable
     * and `.active` to indicate the current page.
     *
     * While the `.disabled` class uses pointer-events: none to try to disable the link functionality of `<a>`, that CSS
     * property is not yet standardized and doesn’t account for keyboard navigation. As such, you should always add
     * `tabindex="-1"` on disabled links and use custom JavaScript to fully disable their functionality.
     *
     * @link https://getbootstrap.com/docs/5.2/components/pagination/#disabled-and-active-states
     */
    public function testsDisabledAndActiveStates(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=1&amp;pagesize=2">Previous</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=1&amp;pagesize=2">1</a></li>
            <li class="page-item"><a class="page-link active" href="/admin/manage?page=2&amp;pagesize=2" aria-current="page">2</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=3&amp;pagesize=2">3</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=3&amp;pagesize=2">Next Page</a></li>
            </ul>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->currentPage(2)
                ->disabledPreviousPage(true)
                ->paginator($this->createOffsetPaginator($this->data, 2))
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testRenderWithIconFirtsLastPage(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=1&amp;pagesize=1"><span aria-hidden="true"><i>«</i></span></a></li>
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=1&amp;pagesize=1">Previous</a></li>
            <li class="page-item"><a class="page-link active" href="/admin/manage?page=1&amp;pagesize=1" aria-current="page">1</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1">2</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=3&amp;pagesize=1">3</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=4&amp;pagesize=1">4</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=1">5</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1">Next Page</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=1"><span aria-hidden="true"><i>»</i></span></a></li>
            </ul>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->currentPage(1)
                ->iconFirtsPage('«')
                ->iconLastPage('»')
                ->paginator($this->createOffsetPaginator($this->data, 1))
                ->urlArguments([])
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testRenderWithIconClassFirtsLastPage(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=1&amp;pagesize=1"><span aria-hidden="true"><i class="bi bi-chevron-double-left p-1"></i></span></a></li>
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=1&amp;pagesize=1">Previous</a></li>
            <li class="page-item"><a class="page-link active" href="/admin/manage?page=1&amp;pagesize=1" aria-current="page">1</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1">2</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=3&amp;pagesize=1">3</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=4&amp;pagesize=1">4</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=1">5</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1">Next Page</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=1"><span aria-hidden="true"><i class="bi bi-chevron-double-right p-1"></i></span></a></li>
            </ul>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->currentPage(1)
                ->iconClassFirtsPage('bi bi-chevron-double-left p-1')
                ->iconClassLastPage('bi bi-chevron-double-right p-1')
                ->paginator($this->createOffsetPaginator($this->data, 1))
                ->urlArguments([])
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testRenderWithLabelFirtsLastPage(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=1&amp;pagesize=1">First</a></li>
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=1&amp;pagesize=1">Previous</a></li>
            <li class="page-item"><a class="page-link active" href="/admin/manage?page=1&amp;pagesize=1" aria-current="page">1</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1">2</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=3&amp;pagesize=1">3</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=4&amp;pagesize=1">4</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=1">5</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1">Next Page</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=1">Last</a></li>
            </ul>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->currentPage(1)
                ->labelFirtsPage('First')
                ->labelLastPage('Last')
                ->paginator($this->createOffsetPaginator($this->data, 1))
                ->urlArguments([])
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testRenderWithUrlQueryParameters(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=1&amp;pagesize=1&amp;filter=test">Previous</a></li>
            <li class="page-item"><a class="page-link active" href="/admin/manage?page=1&amp;pagesize=1&amp;filter=test" aria-current="page">1</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1&amp;filter=test">2</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=3&amp;pagesize=1&amp;filter=test">3</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=4&amp;pagesize=1&amp;filter=test">4</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=1&amp;filter=test">5</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1&amp;filter=test">Next Page</a></li>
            </ul>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->currentPage(1)
                ->paginator($this->createOffsetPaginator($this->data, 1))
                ->urlGenerator(Mock::urlGenerator())
                ->urlQueryParameters(['filter' => 'test'])
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testRenderWithoutPagination(): void
    {
        $this->assertEmpty(
            OffsetPagination::widget()
                ->currentPage(1)
                ->paginator($this->createOffsetPaginator($this->data, 5))
                ->urlArguments([])
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin/manage')
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
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=1&amp;pagesize=1">Previous</a></li>
            <li class="page-item"><a class="page-link active" href="/admin/manage?page=1&amp;pagesize=1" aria-current="page">1</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1">2</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=3&amp;pagesize=1">3</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=4&amp;pagesize=1">4</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=1">5</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1">Next Page</a></li>
            </ul>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->currentPage(1)
                ->paginator($this->createOffsetPaginator($this->data, 1))
                ->urlArguments([])
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * Fancy larger or smaller pagination? Add `.pagination-lg` or `.pagination-sm` for additional sizes.
     *
     * @link https://getbootstrap.com/docs/5.2/components/pagination/#sizing
     */
    public function testsSizing(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav aria-label="Pagination">
            <ul class="pagination pagination-lg">
            <li class="page-item"><a class="page-link active" href="/admin/manage?page=1&amp;pagesize=1" aria-current="page">1</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1">2</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=3&amp;pagesize=1">3</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=4&amp;pagesize=1">4</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=1">5</a></li>
            </ul>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->currentPage(1)
                ->menuClass('pagination pagination-lg')
                ->labelNextPage()
                ->labelPreviousPage()
                ->paginator($this->createOffsetPaginator($this->data, 1))
                ->urlArguments([])
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin/manage')
                ->render(),
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <nav aria-label="Pagination">
            <ul class="pagination pagination-sm">
            <li class="page-item"><a class="page-link active" href="/admin/manage?page=1&amp;pagesize=1" aria-current="page">1</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1">2</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=3&amp;pagesize=1">3</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=4&amp;pagesize=1">4</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=1">5</a></li>
            </ul>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->currentPage(1)
                ->menuClass('pagination pagination-sm')
                ->labelNextPage()
                ->labelPreviousPage()
                ->paginator($this->createOffsetPaginator($this->data, 1))
                ->urlArguments([])
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * Looking to use an icon or symbol in place of text for some pagination links? Be sure to provide proper screen
     * reader support with aria attributes.
     *
     * @link https://getbootstrap.com/docs/5.2/components/pagination/#working-with-icons
     */
    public function testWorkingWithIcons(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=1&amp;pagesize=1"><span aria-hidden="true"><i>«</i></span></a></li>
            <li class="page-item"><a class="page-link active" href="/admin/manage?page=1&amp;pagesize=1" aria-current="page">1</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1">2</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=3&amp;pagesize=1">3</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=4&amp;pagesize=1">4</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=1">5</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1"><span aria-hidden="true"><i>»</i></span></a></li>
            </ul>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->currentPage(1)
                ->iconNextPage('»')
                ->iconPreviousPage('«')
                ->paginator($this->createOffsetPaginator($this->data, 1))
                ->urlArguments([])
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin/manage')
                ->render(),
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=1&amp;pagesize=1"><span aria-hidden="true"><i class="bi bi-chevron-double-left p-1"></i></span></a></li>
            <li class="page-item"><a class="page-link active" href="/admin/manage?page=1&amp;pagesize=1" aria-current="page">1</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1">2</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=3&amp;pagesize=1">3</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=4&amp;pagesize=1">4</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=5&amp;pagesize=1">5</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=1"><span aria-hidden="true"><i class="bi bi-chevron-double-right p-1"></i></span></a></li>
            </ul>
            </nav>
            HTML,
            OffsetPagination::widget()
                ->currentPage(1)
                ->iconClassNextPage('bi bi-chevron-double-right p-1')
                ->iconClassPreviousPage('bi bi-chevron-double-left p-1')
                ->paginator($this->createOffsetPaginator($this->data, 1))
                ->urlArguments([])
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin/manage')
                ->render(),
        );
    }
}
