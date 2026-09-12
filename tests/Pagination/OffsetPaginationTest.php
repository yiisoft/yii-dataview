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
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IdMessageReader;
use Yiisoft\Translator\InMemoryMessageSource;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Yii\DataView\BaseListView;
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
            <span>⟪</span>
            <span>⟨</span>
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
        <span>⟪</span>
        <span>⟨</span>
        <a href="/">1</a>
        <span>⟩</span>
        <span>⟫</span>
        </nav>
        HTML,
        true,
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
            <span>⟪</span>
            <span>⟨</span>
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
            <span>⟪</span>
            <span>⟨</span>
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
            <nav class="pagination-nav" id="main-nav">
            <span>⟪</span>
            <span>⟨</span>
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
            <span>⟪</span>
            <span>⟨</span>
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
            <span>⟪</span>
            <span>⟨</span>
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
            <span>⟪</span>
            <span>⟨</span>
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
            <li><span>⟪</span></li>
            <li><span>⟨</span></li>
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
            <span>⟪</span>
            <span>⟨</span>
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
            <li class="pagination-item" data-type="nav-button"><span>⟪</span></li>
            <li class="pagination-item" data-type="nav-button"><span>⟨</span></li>
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
            <li><span>⟪</span></li>
            <li><span>⟨</span></li>
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
            <li class="disabled"><span>⟪</span></li>
            <li class="disabled"><span>⟨</span></li>
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
            <span class="pagination-link" data-action="navigate">⟪</span>
            <span class="pagination-link" data-action="navigate">⟨</span>
            <a class="pagination-link" data-action="navigate" href="/">1</a>
            <a class="pagination-link" data-action="navigate" href="/page/2">2</a>
            <a class="pagination-link" data-action="navigate" href="/page/2">⟩</a>
            <a class="pagination-link" data-action="navigate" href="/page/2">⟫</a>
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
            <span class="pagination-link" data-action="navigate" role="button">⟪</span>
            <span class="pagination-link" data-action="navigate" role="button">⟨</span>
            <a class="pagination-link" data-action="navigate" role="button" href="/">1</a>
            <a class="pagination-link" data-action="navigate" role="button" href="/page/2">2</a>
            <a class="pagination-link" data-action="navigate" role="button" href="/page/2">⟩</a>
            <a class="pagination-link" data-action="navigate" role="button" href="/page/2">⟫</a>
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
            <span class="btn btn-primary">⟪</span>
            <span class="btn btn-primary">⟨</span>
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
            <span class="btn btn-primary active">⟪</span>
            <span class="btn btn-primary active">⟨</span>
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
            <span class="btn btn-primary">⟪</span>
            <span class="btn btn-primary">⟨</span>
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
            <span class="btn btn-primary disabled">⟪</span>
            <span class="btn btn-primary disabled">⟨</span>
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
            <span>⟪</span>
            <span>Prev</span>
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
            <span>⟪</span>
            <span>⟨</span>
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
            <span>First</span>
            <span>⟨</span>
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
            <span>⟪</span>
            <span>⟨</span>
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
        $context = new PaginationContext('/page/test', '/page/test', '/');

        $this->assertNotSame($widget, $widget->context($context));
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
        $this->assertNotSame($widget, $widget->ariaLabelNav('Pagination'));
        $this->assertNotSame($widget, $widget->ariaLabelFirst('First page'));
        $this->assertNotSame($widget, $widget->ariaLabelPrevious('Previous page'));
        $this->assertNotSame($widget, $widget->ariaLabelNext('Next page'));
        $this->assertNotSame($widget, $widget->ariaLabelLast('Last page'));
        $this->assertNotSame($widget, $widget->ariaLabelPage('Page {page}'));
        $this->assertNotSame($widget, $widget->maxNavLinkCount(5));
    }

    public function testShowOnSinglePageDefaultTrue(): void
    {
        $html = $this->createPagination(1)
            ->showOnSinglePage()
            ->render();

        $this->assertNotSame('', $html);
        $this->assertStringContainsString('<nav>', $html);
    }

    public function testPageRangeMiddlePage(): void
    {
        $html = $this->createPagination(20, 10)
            ->maxNavLinkCount(5)
            ->render();

        $this->assertStringContainsString('<a href="/page/8">8</a>', $html);
        $this->assertStringContainsString('<a href="/page/9">9</a>', $html);
        $this->assertStringContainsString('<a href="/page/10">10</a>', $html);
        $this->assertStringContainsString('<a href="/page/11">11</a>', $html);
        $this->assertStringContainsString('<a href="/page/12">12</a>', $html);
        $this->assertStringNotContainsString('>7</a>', $html);
        $this->assertStringNotContainsString('>13</a>', $html);
    }

    public function testPageRangeNearEnd(): void
    {
        $html = $this->createPagination(10, 9)
            ->maxNavLinkCount(5)
            ->render();

        $this->assertStringContainsString('<a href="/page/6">6</a>', $html);
        $this->assertStringContainsString('<a href="/page/7">7</a>', $html);
        $this->assertStringContainsString('<a href="/page/8">8</a>', $html);
        $this->assertStringContainsString('<a href="/page/9">9</a>', $html);
        $this->assertStringContainsString('<a href="/page/10">10</a>', $html);
        $this->assertStringNotContainsString('>5</a>', $html);
    }

    public function testPageRangeAtEnd(): void
    {
        $html = $this->createPagination(10, 10)
            ->maxNavLinkCount(5)
            ->render();

        $this->assertStringContainsString('<a href="/page/6">6</a>', $html);
        $this->assertStringContainsString('<a href="/page/7">7</a>', $html);
        $this->assertStringContainsString('<a href="/page/8">8</a>', $html);
        $this->assertStringContainsString('<a href="/page/9">9</a>', $html);
        $this->assertStringContainsString('<a href="/page/10">10</a>', $html);
        $this->assertStringNotContainsString('>5</a>', $html);
    }

    public function testPageRangeAtStart(): void
    {
        $html = $this->createPagination(20, 1)
            ->maxNavLinkCount(5)
            ->render();

        $this->assertStringContainsString('<a href="/">1</a>', $html);
        $this->assertStringContainsString('<a href="/page/2">2</a>', $html);
        $this->assertStringContainsString('<a href="/page/3">3</a>', $html);
        $this->assertStringContainsString('<a href="/page/4">4</a>', $html);
        $this->assertStringContainsString('<a href="/page/5">5</a>', $html);
        $this->assertStringNotContainsString('>6</a>', $html);
    }

    public function testPageRangeWithFewerPagesThanMaxLinks(): void
    {
        $html = $this->createPagination(3, 2)
            ->maxNavLinkCount(5)
            ->render();

        $this->assertStringContainsString('<a href="/">1</a>', $html);
        $this->assertStringContainsString('<a href="/page/2">2</a>', $html);
        $this->assertStringContainsString('<a href="/page/3">3</a>', $html);
    }

    public function testPageRangeWithZeroTotalPages(): void
    {
        $data = [];
        $dataReader = new IterableDataReader($data);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(1);
        $pagination = OffsetPagination::create(
            $paginator,
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/',
        )->showOnSinglePage(true);

        $html = $pagination->render();

        $this->assertStringContainsString('<nav>', $html);
        $this->assertStringNotContainsString('href="/page/2"', $html);
    }

    public function testPageRangeRecalculationWhenEndExceedsTotal(): void
    {
        $html = $this->createPagination(6, 5)
            ->maxNavLinkCount(5)
            ->render();

        $this->assertStringContainsString('<a href="/page/2">2</a>', $html);
        $this->assertStringContainsString('<a href="/page/3">3</a>', $html);
        $this->assertStringContainsString('<a href="/page/4">4</a>', $html);
        $this->assertStringContainsString('<a href="/page/5">5</a>', $html);
        $this->assertStringContainsString('<a href="/page/6">6</a>', $html);
        $this->assertStringNotContainsString('>1</a>', $html);
    }

    public function testEnableAccessibility(): void
    {
        $paginator = (new OffsetPaginator(new IterableDataReader(array_fill(0, 3, ['id' => 'uuid']))))
            ->withPageSize(1)
            ->withCurrentPage(1);

        $html = OffsetPagination::create(
            $paginator,
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/',
            accessibility: true,
        )->render();

        $this->assertSame(
            <<<HTML
            <nav aria-label="Pagination">
            <span aria-label="First page" role="link" aria-disabled="true">⟪</span>
            <span aria-label="Previous page" role="link" aria-disabled="true">⟨</span>
            <a aria-label="Page 1" aria-current="page" href="/">1</a>
            <a aria-label="Page 2" href="/page/2">2</a>
            <a aria-label="Page 3" href="/page/3">3</a>
            <a aria-label="Next page" href="/page/2">⟩</a>
            <a aria-label="Last page" href="/page/3">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testAriaLabelsAreNotRenderedWhenAccessibilityDisabled(): void
    {
        $html = $this->createPagination(3, 1)->render();

        $this->assertStringNotContainsString('aria-label', $html);
    }

    public function testAriaLabelsCanBeCustomized(): void
    {
        $paginator = (new OffsetPaginator(new IterableDataReader(array_fill(0, 3, ['id' => 'uuid']))))
            ->withPageSize(1)
            ->withCurrentPage(2);

        $html = OffsetPagination::create(
            $paginator,
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/',
            accessibility: true,
        )
            ->ariaLabelNav('Pages')
            ->ariaLabelFirst('To first')
            ->ariaLabelPrevious('To previous')
            ->ariaLabelNext('To next')
            ->ariaLabelLast('To last')
            ->ariaLabelPage('Open page {page}')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav aria-label="Pages">
            <a aria-label="To first" href="/">⟪</a>
            <a aria-label="To previous" href="/">⟨</a>
            <a aria-label="Open page 1" href="/">1</a>
            <a aria-label="Open page 2" aria-current="page" href="/page/2">2</a>
            <a aria-label="Open page 3" href="/page/3">3</a>
            <a aria-label="To next" href="/page/3">⟩</a>
            <a aria-label="To last" href="/page/3">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testAriaLabelsCanBeDisabledWithNull(): void
    {
        $paginator = (new OffsetPaginator(new IterableDataReader(array_fill(0, 3, ['id' => 'uuid']))))
            ->withPageSize(1)
            ->withCurrentPage(1);

        $html = OffsetPagination::create(
            $paginator,
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/',
            accessibility: true,
        )
            ->ariaLabelNav(null)
            ->ariaLabelFirst(null)
            ->ariaLabelPrevious(null)
            ->ariaLabelNext(null)
            ->ariaLabelLast(null)
            ->ariaLabelPage(null)
            ->render();

        $this->assertStringNotContainsString('aria-label', $html);
    }

    public function testAriaLabelIsNotOverriddenWhenPresentInLinkAttributes(): void
    {
        $paginator = (new OffsetPaginator(new IterableDataReader(array_fill(0, 2, ['id' => 'uuid']))))
            ->withPageSize(1)
            ->withCurrentPage(1);

        $html = OffsetPagination::create(
            $paginator,
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/',
            accessibility: true,
        )
            ->linkAttributes(['aria-label' => 'Custom'])
            ->render();

        $this->assertStringContainsString('<a aria-label="Custom"', $html);
        $this->assertStringNotContainsString('aria-label="Page 1"', $html);
    }

    public function testAriaDisabledCanBeDisabledWithLinkAttributes(): void
    {
        $paginator = (new OffsetPaginator(new IterableDataReader(array_fill(0, 2, ['id' => 'uuid']))))
            ->withPageSize(1)
            ->withCurrentPage(1);

        $html = OffsetPagination::create(
            $paginator,
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/',
            accessibility: true,
        )
            ->linkAttributes(['aria-disabled' => false])
            ->render();

        $this->assertStringNotContainsString('aria-disabled', $html);
        $this->assertStringContainsString('role="link"', $html);
    }

    public function testRoleIsNotOverriddenWhenPresentInLinkAttributes(): void
    {
        $paginator = (new OffsetPaginator(new IterableDataReader(array_fill(0, 2, ['id' => 'uuid']))))
            ->withPageSize(1)
            ->withCurrentPage(1);

        $html = OffsetPagination::create(
            $paginator,
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/',
            accessibility: true,
        )
            ->linkAttributes(['role' => 'button'])
            ->render();

        $this->assertStringContainsString('role="button"', $html);
        $this->assertStringNotContainsString('role="link"', $html);
    }

    public function testAriaCurrentIsNotOverriddenWhenPresentInLinkAttributes(): void
    {
        $paginator = (new OffsetPaginator(new IterableDataReader(array_fill(0, 2, ['id' => 'uuid']))))
            ->withPageSize(1)
            ->withCurrentPage(1);

        $html = OffsetPagination::create(
            $paginator,
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/',
            accessibility: true,
        )
            ->linkAttributes(['aria-current' => 'step'])
            ->render();

        $this->assertStringContainsString('aria-current="step"', $html);
        $this->assertStringNotContainsString('aria-current="page"', $html);
    }

    public function testAriaLabelsAreTranslated(): void
    {
        $paginator = (new OffsetPaginator(new IterableDataReader(array_fill(0, 2, ['id' => 'uuid']))))
            ->withPageSize(1)
            ->withCurrentPage(1);
        $context = new PaginationContext(
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/',
            true,
            $this->createAriaLabelTranslator(),
        );

        $html = OffsetPagination::widget()->paginator($paginator)->context($context)->render();

        $this->assertSame(
            <<<HTML
            <nav aria-label="Seitennavigation">
            <span aria-label="Erste Seite" role="link" aria-disabled="true">⟪</span>
            <span aria-label="Vorherige Seite" role="link" aria-disabled="true">⟨</span>
            <a aria-label="Seite 1" aria-current="page" href="/">1</a>
            <a aria-label="Seite 2" href="/page/2">2</a>
            <a aria-label="Nächste Seite" href="/page/2">⟩</a>
            <a aria-label="Letzte Seite" href="/page/2">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testAriaLabelsAreTranslatedWithTranslatorPassedToCreate(): void
    {
        $paginator = (new OffsetPaginator(new IterableDataReader(array_fill(0, 2, ['id' => 'uuid']))))
            ->withPageSize(1)
            ->withCurrentPage(1);

        $html = OffsetPagination::create(
            $paginator,
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/',
            accessibility: true,
            translator: $this->createAriaLabelTranslator(),
        )->render();

        $this->assertStringContainsString('<nav aria-label="Seitennavigation">', $html);
        $this->assertStringContainsString('<span aria-label="Erste Seite" role="link" aria-disabled="true">⟪</span>', $html);
        $this->assertStringContainsString('<a aria-label="Seite 2" href="/page/2">2</a>', $html);
    }

    public function testAriaLabelPageParametersArePassedToTranslator(): void
    {
        $paginator = (new OffsetPaginator(new IterableDataReader(array_fill(0, 2, ['id' => 'uuid']))))
            ->withPageSize(1)
            ->withCurrentPage(1);
        $translator = new Translator();
        $translator->addCategorySources(
            new CategorySource(
                BaseListView::DEFAULT_TRANSLATION_CATEGORY,
                new IdMessageReader(),
                new SimpleMessageFormatter(),
            ),
        );
        $context = new PaginationContext(
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/page/' . PaginationContext::URL_PLACEHOLDER,
            '/',
            true,
            $translator,
        );

        $html = OffsetPagination::widget()->paginator($paginator)->context($context)->render();

        $this->assertStringContainsString('<a aria-label="Page 1" aria-current="page" href="/">1</a>', $html);
        $this->assertStringContainsString('<a aria-label="Page 2" href="/page/2">2</a>', $html);
    }

    private function createAriaLabelTranslator(): Translator
    {
        $messageSource = new InMemoryMessageSource();
        $messageSource->write(
            BaseListView::DEFAULT_TRANSLATION_CATEGORY,
            'en',
            [
                'Pagination' => 'Seitennavigation',
                'First page' => 'Erste Seite',
                'Previous page' => 'Vorherige Seite',
                'Next page' => 'Nächste Seite',
                'Last page' => 'Letzte Seite',
                'Page {page}' => 'Seite {page}',
            ],
        );

        return (new Translator('en'))->addCategorySources(
            new CategorySource(
                BaseListView::DEFAULT_TRANSLATION_CATEGORY,
                $messageSource,
                new SimpleMessageFormatter(),
            ),
        );
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
