<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\DetailView;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\DetailView\DataField;

final class DataFieldTest extends TestCase
{
    public function testWithoutPropertyAndValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Either "property" or "value" must be set.');
        new DataField();
    }
}
