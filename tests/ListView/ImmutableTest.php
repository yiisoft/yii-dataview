<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\ListView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\ListView;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testImmutable(): void
    {
        $listView = ListView::widget();
        $this->assertNotSame($listView, $listView->afterItem(fn () => ''));
        $this->assertNotSame($listView, $listView->beforeItem(fn () => ''));
        $this->assertNotSame($listView, $listView->itemAttributes([]));
        $this->assertNotSame($listView, $listView->itemView(''));
        $this->assertNotSame($listView, $listView->itemViewParameters([]));
        $this->assertNotSame($listView, $listView->pageParameterType(1));
        $this->assertNotSame($listView, $listView->pageSizeParameterType(1));
        $this->assertNotSame($listView, $listView->previousPageParameterType(1));
        $this->assertNotSame($listView, $listView->separator(''));
        $this->assertNotSame($listView, $listView->sortParameterName(''));
        $this->assertNotSame($listView, $listView->sortParameterType(1));
    }
}
