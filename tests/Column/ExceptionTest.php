<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Column;

final class ExceptionTest extends TestCase
{
    public function testFilterType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid filter type "unknown".');
        Column\DataColumn::create()->filterType('unknown');
    }

    public function testGetUrlGenerator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Url generator is not set');
        Column\ActionColumn::create()->getUrlGenerator();
    }
}
