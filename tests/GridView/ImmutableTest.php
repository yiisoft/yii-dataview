<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
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
        $this->assertNotSame($baseListView, $baseListView->attributes([]));
        $this->assertNotSame($baseListView, $baseListView->emptyText(''));
        $this->assertNotSame($baseListView, $baseListView->emptyTextAttributes([]));
        $this->assertNotSame($baseListView, $baseListView->header(''));
        $this->assertNotSame($baseListView, $baseListView->headerAttributes([]));
        $this->assertNotSame($baseListView, $baseListView->id(''));
        $this->assertNotSame($baseListView, $baseListView->layout(''));
        $this->assertNotSame($baseListView, $baseListView->layoutGridTable(''));
        $this->assertNotSame($baseListView, $baseListView->pagination(''));
        $this->assertNotSame($baseListView, $baseListView->paginator($this->createOffsetPaginator($this->data, 10)));
        $this->assertNotSame($baseListView, $baseListView->sortLinkAttributes([]));
        $this->assertNotSame($baseListView, $baseListView->summary(''));
        $this->assertNotSame($baseListView, $baseListView->summaryAttributes([]));
        $this->assertNotSame($baseListView, $baseListView->toolbar(''));
        $this->assertNotSame($baseListView, $baseListView->translator(Mock::translator('en')));
        $this->assertNotSame($baseListView, $baseListView->urlArguments([]));
        $this->assertNotSame($baseListView, $baseListView->urlGenerator(Mock::urlGenerator()));
        $this->assertNotSame($baseListView, $baseListView->urlName(''));
        $this->assertNotSame($baseListView, $baseListView->urlQueryParameters([]));
        $this->assertNotSame($baseListView, $baseListView->withContainer(false));
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
        $this->assertNotSame($gridView, $gridView->columns([]));
        $this->assertNotSame($gridView, $gridView->columnsGroupEnabled(false));
        $this->assertNotSame($gridView, $gridView->emptyCell(''));
        $this->assertNotSame($gridView, $gridView->filterModelName(''));
        $this->assertNotSame($gridView, $gridView->filterPosition(''));
        $this->assertNotSame($gridView, $gridView->filterRowAttributes([]));
        $this->assertNotSame($gridView, $gridView->footerEnabled(false));
        $this->assertNotSame($gridView, $gridView->footerRowAttributes([]));
        $this->assertNotSame($gridView, $gridView->headerRowAttributes([]));
        $this->assertNotSame($gridView, $gridView->headerTableEnabled(false));
        $this->assertNotSame($gridView, $gridView->rowAttributes([]));
        $this->assertNotSame($gridView, $gridView->tableAttributes([]));
    }

    private function createBaseListView(): DataView\BaseListView
    {
        return new class () extends DataView\BaseListView {
            public function renderItems(): string
            {
                return '';
            }
        };
    }
}
