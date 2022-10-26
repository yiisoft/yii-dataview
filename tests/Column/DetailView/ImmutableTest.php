<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column\DetailView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Column\DetailView\DataColumn;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    public function testImmutable(): void
    {
        $dataColumn = DataColumn::create();
        $this->assertNotSame($dataColumn, $dataColumn->attribute(''));
        $this->assertNotSame($dataColumn, $dataColumn->label(''));
        $this->assertNotSame($dataColumn, $dataColumn->labelAttributes([]));
        $this->assertNotSame($dataColumn, $dataColumn->labelTag(''));
        $this->assertNotSame($dataColumn, $dataColumn->value(''));
        $this->assertNotSame($dataColumn, $dataColumn->valueAttributes([]));
        $this->assertNotSame($dataColumn, $dataColumn->valueTag(''));
    }
}
