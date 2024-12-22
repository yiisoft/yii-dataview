<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\TestCase;
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
        $this->assertNotSame($gridView, $gridView->columnGroupEnabled(false));
        $this->assertNotSame($gridView, $gridView->emptyCell(''));
        $this->assertNotSame($gridView, $gridView->footerEnabled(false));
        $this->assertNotSame($gridView, $gridView->footerRowAttributes([]));
        $this->assertNotSame($gridView, $gridView->headerRowAttributes([]));
        $this->assertNotSame($gridView, $gridView->headerTableEnabled(false));
        $this->assertNotSame($gridView, $gridView->bodyRowAttributes([]));
        $this->assertNotSame($gridView, $gridView->tableAttributes([]));
    }

    private function createBaseListView(): DataView\BaseListView
    {
        return new class () extends DataView\BaseListView {
            public function renderItems(array $items, \Yiisoft\Validator\Result $filterValidationResult): string
            {
                return '';
            }
        };
    }
}
