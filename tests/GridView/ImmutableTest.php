<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use Exception;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Yii\DataView\BaseListView;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\NullUrlParameterProvider;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John', 'age' => 20],
        ['id' => 2, 'name' => 'Mary', 'age' => 21],
    ];

    public function testBaseListView(): void
    {
        $widget = GridView::widget();
        $this->assertNotSame($widget, $widget->containerTag(null));
        $this->assertNotSame($widget, $widget->containerAttributes([]));
        $this->assertNotSame($widget, $widget->noResultsText(''));
        $this->assertNotSame($widget, $widget->noResultsTemplate(''));
        $this->assertNotSame($widget, $widget->noResultsCellAttributes([]));
        $this->assertNotSame($widget, $widget->header(''));
        $this->assertNotSame($widget, $widget->headerAttributes([]));
        $this->assertNotSame($widget, $widget->id(''));
        $this->assertNotSame($widget, $widget->layout(''));
        $this->assertNotSame($widget, $widget->paginationWidget(OffsetPagination::widget()));
        $this->assertNotSame($widget, $widget->dataReader($this->createOffsetPaginator($this->data, 10)));
        $this->assertNotSame($widget, $widget->summaryTemplate(''));
        $this->assertNotSame($widget, $widget->summaryAttributes([]));
        $this->assertNotSame($widget, $widget->toolbar(''));
        $this->assertNotSame($widget, $widget->urlArguments([]));
        $this->assertNotSame($widget, $widget->urlQueryParameters([]));
        $this->assertNotSame($widget, $widget->pageSizeTag(null));
        $this->assertNotSame($widget, $widget->pageSizeAttributes([]));
        $this->assertNotSame($widget, $widget->pageSizeTemplate(null));
        $this->assertNotSame($widget, $widget->pageSizeWidget(null));
        $this->assertNotSame($widget, $widget->pageSizeParameterName(''));
        $this->assertNotSame($widget, $widget->pageParameterName(''));
        $this->assertNotSame($widget, $widget->previousPageParameterName(''));
        $this->assertNotSame($widget, $widget->urlParameterProvider(new NullUrlParameterProvider()));
        $this->assertNotSame($widget, $widget->multiSort());
        $this->assertNotSame($widget, $widget->ignoreMissingPage(true));
        $this->assertNotSame($widget, $widget->pageNotFoundExceptionCallback(null));
        $this->assertNotSame($widget, $widget->urlCreator(null));
        $this->assertNotSame($widget, $widget->offsetPaginationConfig([]));
        $this->assertNotSame($widget, $widget->keysetPaginationConfig([]));
        $this->assertNotSame($widget, $widget->pageSizeConstraint(true));
    }

    public function testGridView(): void
    {
        $gridView = GridView::widget();
        $this->assertNotSame($gridView, $gridView->afterRow(null));
        $this->assertNotSame($gridView, $gridView->beforeRow(null));
        $this->assertNotSame($gridView, $gridView->columns(new DataColumn()));
        $this->assertNotSame($gridView, $gridView->columnGrouping(false));
        $this->assertNotSame($gridView, $gridView->emptyCell(''));
        $this->assertNotSame($gridView, $gridView->enableFooter(false));
        $this->assertNotSame($gridView, $gridView->footerRowAttributes([]));
        $this->assertNotSame($gridView, $gridView->headerRowAttributes([]));
        $this->assertNotSame($gridView, $gridView->enableHeader(false));
        $this->assertNotSame($gridView, $gridView->bodyRowAttributes([]));
        $this->assertNotSame($gridView, $gridView->tableAttributes([]));
        $this->assertNotSame($gridView, $gridView->addColumnRendererConfigs([]));
        $this->assertNotSame($gridView, $gridView->filterCellAttributes([]));
        $this->assertNotSame($gridView, $gridView->filterCellInvalidClass(null));
        $this->assertNotSame($gridView, $gridView->filterErrorsContainerAttributes([]));
        $this->assertNotSame($gridView, $gridView->keepPageOnSort(true));
        $this->assertNotSame($gridView, $gridView->emptyCellAttributes([]));
        $this->assertNotSame($gridView, $gridView->addTableClass(null));
        $this->assertNotSame($gridView, $gridView->tableClass(null));
        $this->assertNotSame($gridView, $gridView->tbodyAttributes([]));
        $this->assertNotSame($gridView, $gridView->addTbodyClass(null));
        $this->assertNotSame($gridView, $gridView->tbodyClass(null));
        $this->assertNotSame($gridView, $gridView->headerCellAttributes([]));
        $this->assertNotSame($gridView, $gridView->bodyCellAttributes([]));
        $this->assertNotSame($gridView, $gridView->sortableLinkAttributes([]));
        $this->assertNotSame($gridView, $gridView->sortableHeaderPrepend(''));
        $this->assertNotSame($gridView, $gridView->sortableHeaderAppend(''));
        $this->assertNotSame($gridView, $gridView->sortableHeaderAscPrepend(''));
        $this->assertNotSame($gridView, $gridView->sortableHeaderAscAppend(''));
        $this->assertNotSame($gridView, $gridView->sortableHeaderDescPrepend(''));
        $this->assertNotSame($gridView, $gridView->sortableHeaderDescAppend(''));
    }

    private function createBaseListView(): BaseListView
    {
        return new class () extends BaseListView {
            public function renderItems(
                array $items,
                Result $filterValidationResult,
                ?ReadableDataInterface $preparedDataReader,
            ): string {
                throw new Exception('Not implemented');
            }

            protected function makeFilters(): array
            {
                throw new Exception('Not implemented');
            }

            protected function prepareOrder(array $order): array
            {
                throw new Exception('Not implemented');
            }
        };
    }
}
