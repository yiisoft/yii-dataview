<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\DataView\Pagination\PaginationContext;
use Yiisoft\Yii\DataView\Pagination\PaginatorNotSupportedException;

final class OffsetPaginationTest extends TestCase
{
    public function testBase(): void
    {
        $html = $this->createPagination(6)->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a href="/">⟪</a>
            <a href="/">⟨</a>
            <a href="/">1</a>
            <a href="/page/2">2</a>
            <a href="/page/3">3</a>
            <a href="/page/4">4</a>
            <a href="/page/5">5</a>
            <a href="/page/6">6</a>
            <a href="/page/2">⟩</a>
            <a href="/page/6">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testNotSupportedPaginator(): void
    {
        $paginator = new KeysetPaginator((new IterableDataReader([]))->withSort(Sort::any(['id'])));
        $widget = new OffsetPagination();

        $this->expectException(PaginatorNotSupportedException::class);
        $this->expectExceptionMessage('Paginator "Yiisoft\Data\Paginator\KeysetPaginator" is not supported.');
        $widget->paginator($paginator);
    }

    public function testRenderWithoutContext(): void
    {
        $data = array_fill(0, 6, ['id' => 'uuid']);
        $dataReader = new IterableDataReader($data);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(1);
        $widget = OffsetPagination::widget()->paginator($paginator);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Context is not set.');
        $widget->render();
    }

    #[TestWith([
        <<<HTML
        <nav>
        <a href="/">⟪</a>
        <a href="/">⟨</a>
        <a href="/">1</a>
        <a href="/">⟩</a>
        <a href="/">⟫</a>
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
            ->createPagination(2)
            ->containerTag('main')
            ->render();

        $this->assertSame(
            <<<HTML
            <main>
            <a href="/">⟪</a>
            <a href="/">⟨</a>
            <a href="/">1</a>
            <a href="/page/2">2</a>
            <a href="/page/2">⟩</a>
            <a href="/page/2">⟫</a>
            </main>
            HTML,
            $html,
        );
    }

    public function testWithoutContainerTag(): void
    {
        $html = $this
            ->createPagination(2)
            ->containerTag(null)
            ->render();

        $this->assertSame(
            <<<HTML
            <a href="/">⟪</a>
            <a href="/">⟨</a>
            <a href="/">1</a>
            <a href="/page/2">2</a>
            <a href="/page/2">⟩</a>
            <a href="/page/2">⟫</a>
            HTML,
            $html,
        );
    }

    public function testContainerTagEmptyString(): void
    {
        $widget = new OffsetPagination();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->containerTag('');
    }

    public function testContainerAttributes(): void
    {
        $html = $this
            ->createPagination(2)
            ->containerAttributes(['class' => 'pagination-nav', 'id' => 'main-nav'])
            ->render();

        $this->assertSame(
            <<<HTML
            <nav id="main-nav" class="pagination-nav">
            <a href="/">⟪</a>
            <a href="/">⟨</a>
            <a href="/">1</a>
            <a href="/page/2">2</a>
            <a href="/page/2">⟩</a>
            <a href="/page/2">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testListTag(): void
    {
        $html = $this
            ->createPagination(2)
            ->listTag('ul')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <ul>
            <a href="/">⟪</a>
            <a href="/">⟨</a>
            <a href="/">1</a>
            <a href="/page/2">2</a>
            <a href="/page/2">⟩</a>
            <a href="/page/2">⟫</a>
            </ul>
            </nav>
            HTML,
            $html,
        );
    }

    public function testWithoutListTag(): void
    {
        $html = $this
            ->createPagination(2)
            ->listTag(null)
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a href="/">⟪</a>
            <a href="/">⟨</a>
            <a href="/">1</a>
            <a href="/page/2">2</a>
            <a href="/page/2">⟩</a>
            <a href="/page/2">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testListTagEmptyString(): void
    {
        $widget = new OffsetPagination();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->listTag('');
    }

    public function testListAttributes(): void
    {
        $html = $this
            ->createPagination(2)
            ->listTag('ul')
            ->listAttributes(['class' => 'pagination-list', 'data-role' => 'navigation'])
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <ul class="pagination-list" data-role="navigation">
            <a href="/">⟪</a>
            <a href="/">⟨</a>
            <a href="/">1</a>
            <a href="/page/2">2</a>
            <a href="/page/2">⟩</a>
            <a href="/page/2">⟫</a>
            </ul>
            </nav>
            HTML,
            $html,
        );
    }

    public function testItemTag(): void
    {
        $html = $this
            ->createPagination(2)
            ->itemTag('li')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <li><a href="/">⟪</a></li>
            <li><a href="/">⟨</a></li>
            <li><a href="/">1</a></li>
            <li><a href="/page/2">2</a></li>
            <li><a href="/page/2">⟩</a></li>
            <li><a href="/page/2">⟫</a></li>
            </nav>
            HTML,
            $html,
        );
    }

    public function testWithoutItemTag(): void
    {
        $html = $this
            ->createPagination(2)
            ->itemTag(null)
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a href="/">⟪</a>
            <a href="/">⟨</a>
            <a href="/">1</a>
            <a href="/page/2">2</a>
            <a href="/page/2">⟩</a>
            <a href="/page/2">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testItemTagEmptyString(): void
    {
        $widget = new OffsetPagination();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->itemTag('');
    }

    public function testItemAttributes(): void
    {
        $html = $this
            ->createPagination(2)
            ->itemTag('li')
            ->itemAttributes(['class' => 'pagination-item', 'data-type' => 'nav-button'])
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <li class="pagination-item" data-type="nav-button"><a href="/">⟪</a></li>
            <li class="pagination-item" data-type="nav-button"><a href="/">⟨</a></li>
            <li class="pagination-item" data-type="nav-button"><a href="/">1</a></li>
            <li class="pagination-item" data-type="nav-button"><a href="/page/2">2</a></li>
            <li class="pagination-item" data-type="nav-button"><a href="/page/2">⟩</a></li>
            <li class="pagination-item" data-type="nav-button"><a href="/page/2">⟫</a></li>
            </nav>
            HTML,
            $html,
        );
    }

    public function testCurrentItemClass(): void
    {
        $html = $this
            ->createPagination(2)
            ->itemTag('li')
            ->currentItemClass('current')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <li><a href="/">⟪</a></li>
            <li><a href="/">⟨</a></li>
            <li class="current"><a href="/">1</a></li>
            <li><a href="/page/2">2</a></li>
            <li><a href="/page/2">⟩</a></li>
            <li><a href="/page/2">⟫</a></li>
            </nav>
            HTML,
            $html,
        );
    }

    public function testDisabledItemClass(): void
    {
        $html = $this
            ->createPagination(5)
            ->itemTag('li')
            ->disabledItemClass('disabled')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <li class="disabled"><a href="/">⟪</a></li>
            <li class="disabled"><a href="/">⟨</a></li>
            <li><a href="/">1</a></li>
            <li><a href="/page/2">2</a></li>
            <li><a href="/page/3">3</a></li>
            <li><a href="/page/4">4</a></li>
            <li><a href="/page/5">5</a></li>
            <li><a href="/page/2">⟩</a></li>
            <li><a href="/page/5">⟫</a></li>
            </nav>
            HTML,
            $html,
        );
    }

    public function testLinkAttributes(): void
    {
        $html = $this
            ->createPagination(2)
            ->linkAttributes(['class' => 'pagination-link', 'data-action' => 'navigate'])
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="pagination-link" href="/" data-action="navigate">⟪</a>
            <a class="pagination-link" href="/" data-action="navigate">⟨</a>
            <a class="pagination-link" href="/" data-action="navigate">1</a>
            <a class="pagination-link" href="/page/2" data-action="navigate">2</a>
            <a class="pagination-link" href="/page/2" data-action="navigate">⟩</a>
            <a class="pagination-link" href="/page/2" data-action="navigate">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testAddLinkAttributes(): void
    {
        $html = $this
            ->createPagination(2)
            ->linkAttributes(['class' => 'pagination-link'])
            ->addLinkAttributes(['data-action' => 'navigate', 'role' => 'button'])
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="pagination-link" href="/" data-action="navigate" role="button">⟪</a>
            <a class="pagination-link" href="/" data-action="navigate" role="button">⟨</a>
            <a class="pagination-link" href="/" data-action="navigate" role="button">1</a>
            <a class="pagination-link" href="/page/2" data-action="navigate" role="button">2</a>
            <a class="pagination-link" href="/page/2" data-action="navigate" role="button">⟩</a>
            <a class="pagination-link" href="/page/2" data-action="navigate" role="button">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testLinkClass(): void
    {
        $html = $this
            ->createPagination(2)
            ->linkClass('btn', 'btn-primary')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="btn btn-primary" href="/">⟪</a>
            <a class="btn btn-primary" href="/">⟨</a>
            <a class="btn btn-primary" href="/">1</a>
            <a class="btn btn-primary" href="/page/2">2</a>
            <a class="btn btn-primary" href="/page/2">⟩</a>
            <a class="btn btn-primary" href="/page/2">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testAddLinkClass(): void
    {
        $html = $this
            ->createPagination(2)
            ->linkClass('btn')
            ->addLinkClass('btn-primary', 'active')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="btn btn-primary active" href="/">⟪</a>
            <a class="btn btn-primary active" href="/">⟨</a>
            <a class="btn btn-primary active" href="/">1</a>
            <a class="btn btn-primary active" href="/page/2">2</a>
            <a class="btn btn-primary active" href="/page/2">⟩</a>
            <a class="btn btn-primary active" href="/page/2">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testCurrentLinkClass(): void
    {
        $html = $this
            ->createPagination(2)
            ->linkClass('btn', 'btn-primary')
            ->currentLinkClass('current')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="btn btn-primary" href="/">⟪</a>
            <a class="btn btn-primary" href="/">⟨</a>
            <a class="btn btn-primary current" href="/">1</a>
            <a class="btn btn-primary" href="/page/2">2</a>
            <a class="btn btn-primary" href="/page/2">⟩</a>
            <a class="btn btn-primary" href="/page/2">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testDisabledLinkClass(): void
    {
        $html = $this
            ->createPagination(5)
            ->linkClass('btn', 'btn-primary')
            ->disabledLinkClass('disabled')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a class="btn btn-primary disabled" href="/">⟪</a>
            <a class="btn btn-primary disabled" href="/">⟨</a>
            <a class="btn btn-primary" href="/">1</a>
            <a class="btn btn-primary" href="/page/2">2</a>
            <a class="btn btn-primary" href="/page/3">3</a>
            <a class="btn btn-primary" href="/page/4">4</a>
            <a class="btn btn-primary" href="/page/5">5</a>
            <a class="btn btn-primary" href="/page/2">⟩</a>
            <a class="btn btn-primary" href="/page/5">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testLabelPrevious(): void
    {
        $html = $this
            ->createPagination(2)
            ->labelPrevious('Prev')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a href="/">⟪</a>
            <a href="/">Prev</a>
            <a href="/">1</a>
            <a href="/page/2">2</a>
            <a href="/page/2">⟩</a>
            <a href="/page/2">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testLabelNext(): void
    {
        $html = $this
            ->createPagination(2)
            ->labelNext('Next')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a href="/">⟪</a>
            <a href="/">⟨</a>
            <a href="/">1</a>
            <a href="/page/2">2</a>
            <a href="/page/2">Next</a>
            <a href="/page/2">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testLabelFirst(): void
    {
        $html = $this
            ->createPagination(2)
            ->labelFirst('First')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a href="/">First</a>
            <a href="/">⟨</a>
            <a href="/">1</a>
            <a href="/page/2">2</a>
            <a href="/page/2">⟩</a>
            <a href="/page/2">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testLabelLast(): void
    {
        $html = $this
            ->createPagination(2)
            ->labelLast('Last')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a href="/">⟪</a>
            <a href="/">⟨</a>
            <a href="/">1</a>
            <a href="/page/2">2</a>
            <a href="/page/2">⟩</a>
            <a href="/page/2">Last</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testMaxNavLinkCount(): void
    {
        $html = $this->createPagination(20, 7)
            ->maxNavLinkCount(3)
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <a href="/">⟪</a>
            <a href="/page/6">⟨</a>
            <a href="/page/6">6</a>
            <a href="/page/7">7</a>
            <a href="/page/8">8</a>
            <a href="/page/8">⟩</a>
            <a href="/page/20">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testCurrentPageExceedsPageCount(): void
    {
        $pagination = $this->createPagination(3, 5);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Current page must be less than or equal to total pages.');
        $pagination->render();
    }

    public function testImmutability(): void
    {
        $widget = new OffsetPagination();

        $this->assertNotSame($widget, $widget->showOnSinglePage());
        $this->assertNotSame($widget, $widget->containerTag('div'));
        $this->assertNotSame($widget, $widget->containerAttributes([]));
        $this->assertNotSame($widget, $widget->listTag('ul'));
        $this->assertNotSame($widget, $widget->listAttributes([]));
        $this->assertNotSame($widget, $widget->itemTag('li'));
        $this->assertNotSame($widget, $widget->itemAttributes([]));
        $this->assertNotSame($widget, $widget->currentItemClass('current'));
        $this->assertNotSame($widget, $widget->disabledItemClass('disabled'));
        $this->assertNotSame($widget, $widget->linkAttributes([]));
        $this->assertNotSame($widget, $widget->addLinkAttributes([]));
        $this->assertNotSame($widget, $widget->linkClass('btn'));
        $this->assertNotSame($widget, $widget->addLinkClass('btn-primary'));
        $this->assertNotSame($widget, $widget->currentLinkClass('current'));
        $this->assertNotSame($widget, $widget->disabledLinkClass('disabled'));
        $this->assertNotSame($widget, $widget->labelPrevious('Prev'));
        $this->assertNotSame($widget, $widget->labelNext('Next'));
        $this->assertNotSame($widget, $widget->labelFirst('First'));
        $this->assertNotSame($widget, $widget->labelLast('Last'));
        $this->assertNotSame($widget, $widget->maxNavLinkCount(5));
    }

    private function createPagination(int $pageCount, ?int $currentPage = null): OffsetPagination
    {
        $data = array_fill(0, $pageCount, ['id' => 'uuid']);
        $dataReader = new IterableDataReader($data);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(1);
        if ($currentPage !== null) {
            $paginator = $paginator->withCurrentPage($currentPage);
        }
        return OffsetPagination::create(
            $paginator,
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/',
        );
    }
}
