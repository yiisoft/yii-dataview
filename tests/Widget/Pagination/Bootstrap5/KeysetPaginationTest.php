<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\Bootstrap5\Pagination\Bootstrap5;

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
                ->paginator($this->createKeysetPaginator($this->data, 2))
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
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=0&amp;pagesize=2">Previous</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=2">Next Page</a></li>
            </ul>
            </nav>
            HTML,
            KeysetPagination::widget()
                ->currentPage(2)
                ->disabledPreviousPage(true)
                ->paginator($this->createKeysetPaginator($this->data, 2))
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
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=0&amp;pagesize=2&amp;filter=test">Previous</a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=2&amp;filter=test">Next Page</a></li>
            </ul>
            </nav>
            HTML,
            KeysetPagination::widget()
                ->currentPage(1)
                ->paginator($this->createKeysetPaginator($this->data, 2))
                ->urlGenerator(Mock::urlGenerator())
                ->urlQueryParameters(['filter' => 'test'])
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
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=0&amp;pagesize=2"><span aria-hidden="true"><i>«</i></span></a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=2"><span aria-hidden="true"><i>»</i></span></a></li>
            </ul>
            </nav>
            HTML,
            KeysetPagination::widget()
                ->currentPage(1)
                ->iconNextPage('»')
                ->iconPreviousPage('«')
                ->paginator($this->createKeysetPaginator($this->data, 2))
                ->urlArguments([])
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin/manage')
                ->render(),
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <nav aria-label="Pagination">
            <ul class="pagination">
            <li class="page-item"><a class="page-link disabled" href="/admin/manage?page=0&amp;pagesize=2"><span aria-hidden="true"><i class="bi bi-chevron-double-left p-1"></i></span></a></li>
            <li class="page-item"><a class="page-link" href="/admin/manage?page=2&amp;pagesize=2"><span aria-hidden="true"><i class="bi bi-chevron-double-right p-1"></i></span></a></li>
            </ul>
            </nav>
            HTML,
            KeysetPagination::widget()
                ->currentPage(1)
                ->iconClassNextPage('bi bi-chevron-double-right p-1')
                ->iconClassPreviousPage('bi bi-chevron-double-left p-1')
                ->paginator($this->createKeysetPaginator($this->data, 2))
                ->urlArguments([])
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin/manage')
                ->render(),
        );
    }
}
