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
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\InMemoryMessageSource;
use Yiisoft\Translator\Translator;
use Yiisoft\Yii\DataView\BaseListView;
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
            <span>⟨</span>
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
        <span>⟨</span>
        <span>⟩</span>
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
            ->createPagination(3)
            ->containerTag('main')
            ->render();

        $this->assertSame(
            <<<HTML
            <main>
            <span>⟨</span>
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
            <span>⟨</span>
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
            <nav class="pagination-nav" id="main-nav">
            <span>⟨</span>
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
            <span>⟨</span>
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
            <span>⟨</span>
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
            <span>⟨</span>
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
            <li><span>⟨</span></li>
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
            <span>⟨</span>
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
            <li class="pagination-item" data-type="nav-button"><span>⟨</span></li>
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
            <li class="disabled"><span>⟨</span></li>
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
            <span class="pagination-link" data-action="navigate">⟨</span>
            <a class="pagination-link" data-action="navigate" href="/next/id1">⟩</a>
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
            <span class="btn btn-primary">⟨</span>
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
            <span class="btn btn-primary active">⟨</span>
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
            <span class="btn btn-primary disabled">⟨</span>
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
            <span>Prev</span>
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
            <span>⟨</span>
            <a href="/next/id1">Next</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testContextImmutability(): void
    {
        $placeholder = PaginationContext::URL_PLACEHOLDER;
        $context1 = new PaginationContext("/next/$placeholder", "/prev/$placeholder", '');
        $context2 = new PaginationContext("/other/$placeholder", "/other/$placeholder", '');

        $widget1 = $this->createPagination(2)->context($context1);
        $widget2 = $widget1->context($context2);

        $html1 = $widget1->render();
        $html2 = $widget2->render();

        $this->assertStringContainsString('/next/', $html1);
        $this->assertStringNotContainsString('/other/', $html1);
        $this->assertStringContainsString('/other/', $html2);
        $this->assertStringNotContainsString('/next/', $html2);
    }

    public function testDefaultShowOnSinglePageIsFalse(): void
    {
        $html = $this->createPagination(1)->render();

        $this->assertSame('', $html);
    }

    public function testShowOnSinglePageDefaultTrue(): void
    {
        $html = $this->createPagination(1)
            ->showOnSinglePage()
            ->render();

        $this->assertNotSame('', $html);
        $this->assertStringContainsString('<nav>', $html);
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
        $this->assertNotSame($widget, $widget->ariaLabelNav('Pagination'));
        $this->assertNotSame($widget, $widget->ariaLabelPrevious('Previous page'));
        $this->assertNotSame($widget, $widget->ariaLabelNext('Next page'));
    }

    public function testEnableAccessibility(): void
    {
        $dataReader = (new IterableDataReader([['id' => 'id1'], ['id' => 'id2'], ['id' => 'id3']]))
            ->withSort(Sort::any(['id']));
        $paginator = (new KeysetPaginator($dataReader))->withPageSize(1);

        $html = KeysetPagination::create(
            $paginator,
            '/next/' . PaginationContext::URL_PLACEHOLDER,
            '/prev/' . PaginationContext::URL_PLACEHOLDER,
            accessibility: true,
        )->render();

        $this->assertSame(
            <<<HTML
            <nav aria-label="Pagination">
            <span aria-label="Previous page" aria-disabled="true">⟨</span>
            <a aria-label="Next page" href="/next/id1">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testAriaLabelsAreNotRenderedWhenAccessibilityDisabled(): void
    {
        $dataReader = (new IterableDataReader([['id' => 'id1'], ['id' => 'id2'], ['id' => 'id3']]))
            ->withSort(Sort::any(['id']));
        $paginator = (new KeysetPaginator($dataReader))->withPageSize(1);

        $html = KeysetPagination::create(
            $paginator,
            '/next/' . PaginationContext::URL_PLACEHOLDER,
            '/prev/' . PaginationContext::URL_PLACEHOLDER,
        )->render();

        $this->assertStringNotContainsString('aria-label', $html);
    }

    public function testAriaLabelsCanBeCustomizedAndDisabled(): void
    {
        $dataReader = (new IterableDataReader([['id' => 'id1'], ['id' => 'id2'], ['id' => 'id3']]))
            ->withSort(Sort::any(['id']));
        $paginator = (new KeysetPaginator($dataReader))->withPageSize(1);

        $html = KeysetPagination::create(
            $paginator,
            '/next/' . PaginationContext::URL_PLACEHOLDER,
            '/prev/' . PaginationContext::URL_PLACEHOLDER,
            accessibility: true,
        )
            ->ariaLabelNav(null)
            ->ariaLabelPrevious('To previous')
            ->ariaLabelNext('To next')
            ->render();

        $this->assertSame(
            <<<HTML
            <nav>
            <span aria-label="To previous" aria-disabled="true">⟨</span>
            <a aria-label="To next" href="/next/id1">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testAriaLabelsAreTranslated(): void
    {
        $messageSource = new InMemoryMessageSource();
        $messageSource->write(
            BaseListView::DEFAULT_TRANSLATION_CATEGORY,
            'en',
            [
                'Pagination' => 'Seitennavigation',
                'Previous page' => 'Vorherige Seite',
                'Next page' => 'Nächste Seite',
            ],
        );
        $translator = (new Translator('en'))->addCategorySources(
            new CategorySource(BaseListView::DEFAULT_TRANSLATION_CATEGORY, $messageSource),
        );

        $dataReader = (new IterableDataReader([['id' => 'id1'], ['id' => 'id2'], ['id' => 'id3']]))
            ->withSort(Sort::any(['id']));
        $paginator = (new KeysetPaginator($dataReader))->withPageSize(1);
        $context = new PaginationContext(
            '/next/' . PaginationContext::URL_PLACEHOLDER,
            '/prev/' . PaginationContext::URL_PLACEHOLDER,
            '/',
            true,
            $translator,
        );

        $html = KeysetPagination::widget()->paginator($paginator)->context($context)->render();

        $this->assertSame(
            <<<HTML
            <nav aria-label="Seitennavigation">
            <span aria-label="Vorherige Seite" aria-disabled="true">⟨</span>
            <a aria-label="Nächste Seite" href="/next/id1">⟩</a>
            </nav>
            HTML,
            $html,
        );
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
