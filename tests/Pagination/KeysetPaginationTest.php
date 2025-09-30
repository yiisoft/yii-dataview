<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\DataView\Pagination\KeysetPagination;
use Yiisoft\Yii\DataView\Pagination\PaginationContext;
use Yiisoft\Yii\DataView\Pagination\PaginatorNotSupportedException;

final class KeysetPaginationTest extends TestCase
{
    public function testBase(): void
    {
        $data = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
            ['id' => 4],
            ['id' => 5],
        ];

        $html = $this->createPagination($data, Sort::any(['id']))->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a>⟨</a>
            <a href="/next/2">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testNotSupportedPaginator(): void
    {
        $paginator = new OffsetPaginator(new IterableDataReader([]));
        $widget = new KeysetPagination();

        $this->expectException(PaginatorNotSupportedException::class);
        $this->expectExceptionMessage('Paginator "Yiisoft\Data\Paginator\OffsetPaginator" is not supported.');
        $widget->paginator($paginator);
    }

    #[TestWith([
        <<<HTML
        <nav>
        <a>⟨</a>
        <a>⟩</a>
        </nav>
        HTML,
        true
    ])]
    #[TestWith(['', false])]
    public function testShowOnSinglePage(string $expected, bool $show): void
    {
        $html = $this
            ->createPagination([['id' => 1]], Sort::any(['id']))
            ->showOnSinglePage($show)
            ->render();

        $this->assertSame($expected, $html);
    }

    public function testContainerTag(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->containerTag('main')
            ->render();

        $this->assertSame(
            <<<HTML
            <main>
            <a>⟨</a>
            <a href="/next/2">⟩</a>
            </main>
            HTML,
            $html,
        );
    }

    public function testWithoutContainerTag(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->containerTag(null)
            ->render();

        $this->assertSame(
            <<<HTML
            <a>⟨</a>
            <a href="/next/2">⟩</a>
            HTML,
            $html,
        );
    }

    public function testContainerTagEmptyString(): void
    {
        $widget = new KeysetPagination();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->containerTag('');
    }

    public function testContainerAttributes(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->containerAttributes(['class' => 'pagination-nav', 'id' => 'main-nav'])
            ->render();

        $this->assertSame(
            <<<HTML
            <nav id="main-nav" class="pagination-nav">
            <a>⟨</a>
            <a href="/next/2">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testListTag(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->listTag('ul')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <ul>
            <a>⟨</a>
            <a href="/next/2">⟩</a>
            </ul>
            </nav>
            HTML,
            $html,
        );
    }

    public function testWithoutListTag(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->listTag(null)
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a>⟨</a>
            <a href="/next/2">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testListTagEmptyString(): void
    {
        $widget = new KeysetPagination();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->listTag('');
    }

    public function testListAttributes(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->listTag('ul')
            ->listAttributes(['class' => 'pagination-list', 'data-role' => 'navigation'])
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <ul class="pagination-list" data-role="navigation">
            <a>⟨</a>
            <a href="/next/2">⟩</a>
            </ul>
            </nav>
            HTML,
            $html,
        );
    }

    public function testItemTag(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->itemTag('li')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <li><a>⟨</a></li>
            <li><a href="/next/2">⟩</a></li>
            </nav>
            HTML,
            $html,
        );
    }

    public function testWithoutItemTag(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->itemTag(null)
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a>⟨</a>
            <a href="/next/2">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testItemTagEmptyString(): void
    {
        $widget = new KeysetPagination();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->itemTag('');
    }

    public function testItemAttributes(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->itemTag('li')
            ->itemAttributes(['class' => 'pagination-item', 'data-type' => 'nav-button'])
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <li class="pagination-item" data-type="nav-button"><a>⟨</a></li>
            <li class="pagination-item" data-type="nav-button"><a href="/next/2">⟩</a></li>
            </nav>
            HTML,
            $html,
        );
    }

    public function testDisabledItemClass(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->itemTag('li')
            ->disabledItemClass('disabled')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <li class="disabled"><a>⟨</a></li>
            <li><a href="/next/2">⟩</a></li>
            </nav>
            HTML,
            $html,
        );
    }

    public function testLinkAttributes(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->linkAttributes(['class' => 'pagination-link', 'data-action' => 'navigate'])
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="pagination-link" data-action="navigate">⟨</a>
            <a class="pagination-link" href="/next/2" data-action="navigate">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testLinkClass(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->linkClass('btn', 'btn-primary')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="btn btn-primary">⟨</a>
            <a class="btn btn-primary" href="/next/2">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testAddLinkClass(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->linkClass('btn')
            ->addLinkClass('btn-primary', 'active')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="btn btn-primary active">⟨</a>
            <a class="btn btn-primary active" href="/next/2">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testDisabledLinkClass(): void
    {
        $html = $this
            ->createPagination([['id' => 1], ['id' => 2], ['id' => 3]], Sort::any(['id']))
            ->linkClass('btn', 'btn-primary')
            ->disabledLinkClass('disabled')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="btn btn-primary disabled">⟨</a>
            <a class="btn btn-primary" href="/next/2">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    private function createPagination(array $data, Sort $sort, int $pageSize = 2): KeysetPagination
    {
        $dataReader = (new IterableDataReader($data))->withSort($sort);
        $paginator = (new KeysetPaginator($dataReader))->withPageSize($pageSize);
        return KeysetPagination::create(
            $paginator,
            '/next/' . PaginationContext::URL_PLACEHOLDER,
            '/prev/' . PaginationContext::URL_PLACEHOLDER,
        );
    }
}
