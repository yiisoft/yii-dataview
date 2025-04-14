<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    public function testOffsetPagination(): void
    {
        $offsetPagination = OffsetPagination::widget();
        $this->assertNotSame($offsetPagination, $offsetPagination->addLinkClass(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->containerAttributes([]));
        $this->assertNotSame($offsetPagination, $offsetPagination->containerTag('div'));
        $this->assertNotSame($offsetPagination, $offsetPagination->currentLinkClass(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->disabledLinkClass(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->labelFirst(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->labelLast(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->labelNext(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->labelPrevious(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->linkClass(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->maxNavLinkCount(10));
    }
}
