<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\LinkSorter;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Router\Route;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\Widget\LinkSorter;

final class LinkSorterTest extends TestCase
{
    use TestTrait;

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testAttribute(): void
    {
        $this->assertSame(
            <<<HTML
            <a href="/admin/manage?page=2&amp;pagesize=5&amp;sort=-id" data-sort="-id">Id</a>
            HTML,
            LinkSorter::widget()
                ->attribute('id')
                ->attributes(['id' => SORT_ASC, 'username' => SORT_DESC, 'default' => 'desc'])
                ->currentPage(2)
                ->pageSize(5)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testAttributeNoExist(): void
    {
        $this->assertEmpty(
            LinkSorter::widget()
                ->attribute('name')
                ->attributes(['id' => SORT_ASC, 'username' => SORT_DESC, 'default' => 'desc'])
                ->currentPage(2)
                ->pageSize(5)
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testDirectionAsc(): void
    {
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
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testDirectionDesc(): void
    {
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
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testDirectionIconClassAsc(): void
    {
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
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testDirectionIconClassDesc(): void
    {
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
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testMultisort(): void
    {
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
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testLabel(): void
    {
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
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testLinkAttributes(): void
    {
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
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testPageConfig(): void
    {
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
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testRender(): void
    {
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
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testUrlArguments(): void
    {
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
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testUrlEnabledArgumentsWithFalse(): void
    {
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
                ->urlName('admin/manage')
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testUrlNameWithNull(): void
    {
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
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testUrlQueryParametersWithUrlArgumentsFalse(): void
    {
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
                ->urlName('admin/manage')
                ->render(),
        );
    }
}
