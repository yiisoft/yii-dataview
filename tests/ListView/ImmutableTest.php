<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\ListView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\ListView;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    public function testInmutable(): void
    {
        $listView = ListView::widget();
        $this->assertNotSame($listView, $listView->afterItem(fn () => ''));
        $this->assertNotSame($listView, $listView->beforeItem(fn () => ''));
        $this->assertNotSame($listView, $listView->itemView(''));
        $this->assertNotSame($listView, $listView->itemViewAttributes([]));
        $this->assertNotSame($listView, $listView->separator(''));
        $this->assertNotSame($listView, $listView->viewParams([]));
        $this->assertNotSame($listView, $listView->webView(Mock::webView()));
    }
}
