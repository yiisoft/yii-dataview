<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\DataReaderNotSetException;

final class DataReaderNotSetExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $exception = new DataReaderNotSetException();

        $this->assertSame(
            'Failed to create widget because "dataReader" is not set.',
            $exception->getMessage(),
        );
    }
}
