<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Field;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Field\DataField;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    public function testDetailColumn(): void
    {
        $dataField = DataField::create();
        $this->assertNotSame($dataField, $dataField->attribute(''));
        $this->assertNotSame($dataField, $dataField->label(''));
        $this->assertNotSame($dataField, $dataField->labelAttributes([]));
        $this->assertNotSame($dataField, $dataField->labelTag(''));
        $this->assertNotSame($dataField, $dataField->value(''));
        $this->assertNotSame($dataField, $dataField->valueAttributes([]));
        $this->assertNotSame($dataField, $dataField->valueTag(''));
    }
}
