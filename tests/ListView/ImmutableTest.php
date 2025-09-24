<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\ListView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\ListView\ListView;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    public function testImmutable(): void
    {
        $listView = ListView::widget();
        $this->assertNotSame($listView, $listView->afterItem(fn () => ''));
        $this->assertNotSame($listView, $listView->beforeItem(fn () => ''));
        $this->assertNotSame($listView, $listView->itemAttributes([]));
        $this->assertNotSame($listView, $listView->itemContent(''));
        $this->assertNotSame($listView, $listView->itemViewParameters([]));
        $this->assertNotSame($listView, $listView->pageParameterType(1));
        $this->assertNotSame($listView, $listView->pageSizeParameterType(1));
        $this->assertNotSame($listView, $listView->previousPageParameterType(1));
        $this->assertNotSame($listView, $listView->separator(''));
        $this->assertNotSame($listView, $listView->sortParameterName(''));
        $this->assertNotSame($listView, $listView->sortParameterType(1));
        $this->assertNotSame($listView, $listView->append(''));
        $this->assertNotSame($listView, $listView->prepend(''));
    }
}
