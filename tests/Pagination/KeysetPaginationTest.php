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
        $html = $this->createPagination(2)->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a>⟨</a>
            <a href="/next/id1">⟩</a>
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
            ->createPagination(1)
            ->showOnSinglePage($show)
            ->render();

        $this->assertSame($expected, $html);
    }

    public function testContainerTag(): void
    {
        $html = $this
            ->createPagination(3)
            ->containerTag('main')
            ->render();

        $this->assertSame(
            <<<HTML
            <main>
            <a>⟨</a>
            <a href="/next/id1">⟩</a>
            </main>
            HTML,
            $html,
        );
    }

    public function testWithoutContainerTag(): void
    {
        $html = $this
            ->createPagination(3)
            ->containerTag(null)
            ->render();

        $this->assertSame(
            <<<HTML
            <a>⟨</a>
            <a href="/next/id1">⟩</a>
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
            ->createPagination(3)
            ->containerAttributes(['class' => 'pagination-nav', 'id' => 'main-nav'])
            ->render();

        $this->assertSame(
            <<<HTML
            <nav id="main-nav" class="pagination-nav">
            <a>⟨</a>
            <a href="/next/id1">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testListTag(): void
    {
        $html = $this
            ->createPagination(3)
            ->listTag('ul')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <ul>
            <a>⟨</a>
            <a href="/next/id1">⟩</a>
            </ul>
            </nav>
            HTML,
            $html,
        );
    }

    public function testWithoutListTag(): void
    {
        $html = $this
            ->createPagination(3)
            ->listTag(null)
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a>⟨</a>
            <a href="/next/id1">⟩</a>
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
            ->createPagination(3)
            ->listTag('ul')
            ->listAttributes(['class' => 'pagination-list', 'data-role' => 'navigation'])
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <ul class="pagination-list" data-role="navigation">
            <a>⟨</a>
            <a href="/next/id1">⟩</a>
            </ul>
            </nav>
            HTML,
            $html,
        );
    }

    public function testItemTag(): void
    {
        $html = $this
            ->createPagination(3)
            ->itemTag('li')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <li><a>⟨</a></li>
            <li><a href="/next/id1">⟩</a></li>
            </nav>
            HTML,
            $html,
        );
    }

    public function testWithoutItemTag(): void
    {
        $html = $this
            ->createPagination(3)
            ->itemTag(null)
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a>⟨</a>
            <a href="/next/id1">⟩</a>
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
            ->createPagination(3)
            ->itemTag('li')
            ->itemAttributes(['class' => 'pagination-item', 'data-type' => 'nav-button'])
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <li class="pagination-item" data-type="nav-button"><a>⟨</a></li>
            <li class="pagination-item" data-type="nav-button"><a href="/next/id1">⟩</a></li>
            </nav>
            HTML,
            $html,
        );
    }

    public function testDisabledItemClass(): void
    {
        $html = $this
            ->createPagination(2)
            ->itemTag('li')
            ->disabledItemClass('disabled')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <li class="disabled"><a>⟨</a></li>
            <li><a href="/next/id1">⟩</a></li>
            </nav>
            HTML,
            $html,
        );
    }

    public function testLinkAttributes(): void
    {
        $html = $this
            ->createPagination(3)
            ->linkAttributes(['class' => 'pagination-link', 'data-action' => 'navigate'])
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="pagination-link" data-action="navigate">⟨</a>
            <a class="pagination-link" href="/next/id1" data-action="navigate">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testLinkClass(): void
    {
        $html = $this
            ->createPagination(3)
            ->linkClass('btn', 'btn-primary')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="btn btn-primary">⟨</a>
            <a class="btn btn-primary" href="/next/id1">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testAddLinkClass(): void
    {
        $html = $this
            ->createPagination(3)
            ->linkClass('btn')
            ->addLinkClass('btn-primary', 'active')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="btn btn-primary active">⟨</a>
            <a class="btn btn-primary active" href="/next/id1">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testDisabledLinkClass(): void
    {
        $html = $this
            ->createPagination(3)
            ->linkClass('btn', 'btn-primary')
            ->disabledLinkClass('disabled')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="btn btn-primary disabled">⟨</a>
            <a class="btn btn-primary" href="/next/id1">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testLabelPrevious(): void
    {
        $html = $this
            ->createPagination(3)
            ->labelPrevious('Prev')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a>Prev</a>
            <a href="/next/id1">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testLabelNext(): void
    {
        $html = $this
            ->createPagination(3)
            ->labelNext('Next')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a>⟨</a>
            <a href="/next/id1">Next</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testImmutability(): void
    {
        $widget = new KeysetPagination();

        $this->assertNotSame($widget, $widget->showOnSinglePage());
        $this->assertNotSame($widget, $widget->containerTag('div'));
        $this->assertNotSame($widget, $widget->containerAttributes([]));
        $this->assertNotSame($widget, $widget->listTag('ul'));
        $this->assertNotSame($widget, $widget->listAttributes([]));
        $this->assertNotSame($widget, $widget->itemTag('li'));
        $this->assertNotSame($widget, $widget->itemAttributes([]));
        $this->assertNotSame($widget, $widget->disabledItemClass('disabled'));
        $this->assertNotSame($widget, $widget->linkAttributes([]));
        $this->assertNotSame($widget, $widget->linkClass('btn'));
        $this->assertNotSame($widget, $widget->addLinkClass('btn-primary'));
        $this->assertNotSame($widget, $widget->disabledLinkClass('disabled'));
        $this->assertNotSame($widget, $widget->labelPrevious('Prev'));
        $this->assertNotSame($widget, $widget->labelNext('Next'));
    }

    private function createPagination(int $pageCount): KeysetPagination
    {
        $data = [];
        for ($i = 1; $i <= $pageCount; $i++) {
            $data[] = ['id' => 'id' . $i];
        }

        $dataReader = (new IterableDataReader($data))->withSort(Sort::any(['id']));
        $paginator = (new KeysetPaginator($dataReader))->withPageSize(1);
        return KeysetPagination::create(
            $paginator,
            '/next/' . PaginationContext::URL_PLACEHOLDER,
            '/prev/' . PaginationContext::URL_PLACEHOLDER,
        );
    }
}
