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
        $this->assertNotSame($offsetPagination, $offsetPagination->maxNavLinkCount(10));
    }
}
