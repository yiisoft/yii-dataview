<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use Exception;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView;
use Yiisoft\Yii\DataView\Column\DataColumn;
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
        $baseListView = $this->createBaseListView();
        $this->assertNotSame($baseListView, $baseListView->containerTag(null));
        $this->assertNotSame($baseListView, $baseListView->containerAttributes([]));
        $this->assertNotSame($baseListView, $baseListView->emptyText(''));
        $this->assertNotSame($baseListView, $baseListView->emptyTextAttributes([]));
        $this->assertNotSame($baseListView, $baseListView->header(''));
        $this->assertNotSame($baseListView, $baseListView->headerAttributes([]));
        $this->assertNotSame($baseListView, $baseListView->id(''));
        $this->assertNotSame($baseListView, $baseListView->layout(''));
        $this->assertNotSame($baseListView, $baseListView->paginationWidget(OffsetPagination::widget()));
        $this->assertNotSame($baseListView, $baseListView->dataReader($this->createOffsetPaginator($this->data, 10)));
        $this->assertNotSame($baseListView, $baseListView->summaryTemplate(''));
        $this->assertNotSame($baseListView, $baseListView->summaryAttributes([]));
        $this->assertNotSame($baseListView, $baseListView->toolbar(''));
        $this->assertNotSame($baseListView, $baseListView->urlArguments([]));
        $this->assertNotSame($baseListView, $baseListView->urlQueryParameters([]));
        $this->assertNotSame($baseListView, $baseListView->pageSizeTag(null));
        $this->assertNotSame($baseListView, $baseListView->pageSizeAttributes([]));
        $this->assertNotSame($baseListView, $baseListView->pageSizeTemplate(null));
        $this->assertNotSame($baseListView, $baseListView->pageSizeWidget(null));
        $this->assertNotSame($baseListView, $baseListView->pageSizeParameterName(''));
        $this->assertNotSame($baseListView, $baseListView->pageParameterName(''));
        $this->assertNotSame($baseListView, $baseListView->previousPageParameterName(''));
        $this->assertNotSame($baseListView, $baseListView->urlParameterProvider(null));
        $this->assertNotSame($baseListView, $baseListView->multiSort());
        $this->assertNotSame($baseListView, $baseListView->ignoreMissingPage(true));
        $this->assertNotSame($baseListView, $baseListView->pageNotFoundExceptionCallback(null));
        $this->assertNotSame($baseListView, $baseListView->urlCreator(null));
        $this->assertNotSame($baseListView, $baseListView->offsetPaginationConfig([]));
        $this->assertNotSame($baseListView, $baseListView->keysetPaginationConfig([]));
        $this->assertNotSame($baseListView, $baseListView->pageSizeConstraint(true));
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testGridView(): void
    {
        $gridView = DataView\GridView::widget();
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

    private function createBaseListView(): DataView\BaseListView
    {
        return new class () extends DataView\BaseListView {
            public function renderItems(
                array $items,
                \Yiisoft\Validator\Result $filterValidationResult,
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
