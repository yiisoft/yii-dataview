<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\DetailView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    public function testInmutable(): void
    {
        $detailView = DetailView::widget();
        $this->assertNotSame($detailView, $detailView->attributes([]));
        $this->assertNotSame($detailView, $detailView->columns([]));
        $this->assertNotSame($detailView, $detailView->containerAttributes([]));
        $this->assertNotSame($detailView, $detailView->data([]));
        $this->assertNotSame($detailView, $detailView->dataAttributes([]));
        $this->assertNotSame($detailView, $detailView->header(''));
        $this->assertNotSame($detailView, $detailView->itemTemplate(''));
        $this->assertNotSame($detailView, $detailView->labelAttributes([]));
        $this->assertNotSame($detailView, $detailView->labelTag(''));
        $this->assertNotSame($detailView, $detailView->labelTemplate(''));
        $this->assertNotSame($detailView, $detailView->template(''));
        $this->assertNotSame($detailView, $detailView->valueAttributes([]));
        $this->assertNotSame($detailView, $detailView->valueFalse(''));
        $this->assertNotSame($detailView, $detailView->valueTag(''));
        $this->assertNotSame($detailView, $detailView->valueTemplate(''));
        $this->assertNotSame($detailView, $detailView->valueTrue(''));
    }
}
