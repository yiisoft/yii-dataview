<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\LinkSorter;

use PHPUnit\Framework\TestCase;
use Yiisoft\Router\Route;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\Widget\LinkSorter;

final class LinkSorterTest extends TestCase
{
    use TestTrait;

    public function testAttribute(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a href="/admin/manage?page=2&amp;pagesize=5&amp;sort=-id" data-sort="-id">Id</a>
            HTML,
            LinkSorter::widget()
                ->attribute('id')
                ->attributes(['id' => SORT_ASC, 'username' => SORT_DESC, 'default' => 'desc'])
                ->currentPage(2)
                ->pageSize(5)
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testAttributeNoExist(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertEmpty(
            LinkSorter::widget()
                ->attribute('name')
                ->attributes(['id' => SORT_ASC, 'username' => SORT_DESC, 'default' => 'desc'])
                ->currentPage(2)
                ->pageSize(5)
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testDirectionAsc(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a class="asc" href="/admin/manage?page=2&amp;pagesize=5&amp;sort=-id" data-sort="-id">Id <i>&#x2191;</i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('id')
                ->attributes(['id' => SORT_ASC, 'username' => SORT_DESC])
                ->currentPage(2)
                ->directions(['id' => 'asc'])
                ->pageSize(5)
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testDirectionDesc(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a class="desc" href="/admin/manage?page=2&amp;pagesize=5&amp;sort=id" data-sort="id">Id <i>&#x2193;</i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('id')
                ->attributes(['id' => SORT_ASC, 'username' => SORT_DESC])
                ->currentPage(2)
                ->directions(['id' => 'desc'])
                ->pageSize(5)
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testDirectionIconClassAsc(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a class="asc" href="/admin/manage?page=3&amp;pagesize=5&amp;sort=-username" data-sort="-username">Username <i class="bi bi-sort-alpha-up"></i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('username')
                ->attributes(['id' => SORT_ASC, 'username' => SORT_DESC])
                ->currentPage(3)
                ->directions(['username' => 'asc'])
                ->iconAscClass('bi bi-sort-alpha-up')
                ->pageSize(5)
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testDirectionIconClassDesc(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a class="desc" href="/admin/manage?page=3&amp;pagesize=5&amp;sort=username" data-sort="username">Username <i class="bi bi-sort-alpha-down"></i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('username')
                ->attributes(['id' => SORT_ASC, 'username' => SORT_ASC])
                ->currentPage(3)
                ->directions(['username' => 'desc'])
                ->iconDescClass('bi bi-sort-alpha-down')
                ->pageSize(5)
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testMultisort(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a class="desc" href="/admin/manage?page=1&amp;pagesize=5&amp;sort=username%2C-id" data-sort="username,-id">Username <i>&#x2193;</i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('username')
                ->attributes(['id' => SORT_ASC, 'username' => SORT_DESC])
                ->currentPage(1)
                ->directions(['id' => 'desc', 'username' => 'desc'])
                ->pageSize(5)
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testLabel(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a class="text-danger asc" href="/admin/manage?page=1&amp;pagesize=5&amp;sort=-id" data-sort="-id">Id <i>&#x2191;</i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('id')
                ->attributes(['id' => SORT_ASC, 'username' => SORT_DESC])
                ->currentPage(1)
                ->directions(['id' => SORT_ASC])
                ->linkAttributes(['class' => 'text-danger'])
                ->pageSize(5)
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testLinkAttributes(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a class="text-danger desc" href="/admin/manage?page=1&amp;pagesize=5&amp;sort=id" data-sort="id">Id <i>&#x2193;</i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('id')
                ->attributes(['id' => SORT_ASC, 'username' => SORT_DESC])
                ->currentPage(1)
                ->directions(['id' => 'desc'])
                ->linkAttributes(['class' => 'text-danger'])
                ->pageSize(5)
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testPageConfig(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a class="desc" href="/admin/manage?page=1&amp;pagesize=5&amp;sort=username" data-sort="username">Username <i>&#x2193;</i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('username')
                ->attributes(
                    [
                        'id' => SORT_ASC,
                        'username' => SORT_ASC,
                    ],
                )
                ->currentPage(1)
                ->directions(['username' => 'desc'])
                ->pageSize(5)
                ->pageConfig(['page' => 1, 'pagesize' => 5])
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testRender(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a class="desc" href="/admin/manage?page=1&amp;pagesize=5&amp;sort=username" data-sort="username">Username <i>&#x2193;</i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('username')
                ->attributes(
                    [
                        'id' => SORT_ASC,
                        'username' => SORT_ASC,
                    ],
                )
                ->currentPage(1)
                ->directions(['username' => 'desc'])
                ->pageSize(5)
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testUrlArguments(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a class="asc" href="/admin/manage?test=test&amp;page=1&amp;pagesize=5&amp;sort=-id%2C-username" data-sort="-id,-username">Id <i class="bi bi-sort-alpha-up"></i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('id')
                ->attributes(['id' => SORT_DESC, 'username' => SORT_ASC])
                ->currentPage(1)
                ->directions(['id' => 'asc', 'username' => 'desc'])
                ->iconAscClass('bi bi-sort-alpha-up')
                ->pageSize(5)
                ->urlArguments(['test' => 'test'])
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testUrlEnabledArgumentsWithFalse(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a class="asc" href="/admin/manage?page=1&amp;pagesize=5&amp;sort=-id%2C-username" data-sort="-id,-username">Id <i class="bi bi-sort-alpha-up"></i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('id')
                ->attributes(['id' => SORT_DESC, 'username' => SORT_ASC])
                ->currentPage(1)
                ->directions(['id' => 'asc', 'username' => 'desc'])
                ->iconAscClass('bi bi-sort-alpha-up')
                ->pageSize(5)
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    public function testUrlNameWithNull(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a class="asc" href="?test=test&amp;page=1&amp;pagesize=5&amp;sort=-id%2C-username" data-sort="-id,-username">Id <i class="bi bi-sort-alpha-up"></i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('id')
                ->attributes(['id' => SORT_DESC, 'username' => SORT_ASC])
                ->currentPage(1)
                ->directions(['id' => 'asc', 'username' => 'desc'])
                ->iconAscClass('bi bi-sort-alpha-up')
                ->pageSize(5)
                ->urlQueryParameters(['test' => 'test'])
                ->urlGenerator($urlGenerator)
                ->render(),
        );
    }

    public function testUrlQueryParametersWithUrlArgumentsFalse(): void
    {
        $urlGenerator = Mock::UrlGenerator([Route::get('/admin/manage')->name('admin/manage')]);

        $this->assertSame(
            <<<HTML
            <a class="asc" href="/admin/manage?test=test&amp;page=1&amp;pagesize=5&amp;sort=-id%2C-username" data-sort="-id,-username">Id <i class="bi bi-sort-alpha-up"></i></a>
            HTML,
            LinkSorter::widget()
                ->attribute('id')
                ->attributes(['id' => SORT_DESC, 'username' => SORT_ASC])
                ->currentPage(1)
                ->directions(['id' => 'asc', 'username' => 'desc'])
                ->iconAscClass('bi bi-sort-alpha-up')
                ->pageSize(5)
                ->urlQueryParameters(['test' => 'test'])
                ->urlGenerator($urlGenerator)
                ->urlName('admin/manage')
                ->render(),
        );
    }
}
