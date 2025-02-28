<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Yii\DataView\Pagination\PaginatorNotSupportedException;

/**
 * @covers \Yiisoft\Yii\DataView\Pagination\PaginatorNotSupportedException
 */
final class PaginatorNotSupportedExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $paginator = $this->createMock(PaginatorInterface::class);
        
        $exception = new PaginatorNotSupportedException($paginator);
        
        $expectedMessage = sprintf(
            'Paginator "%s" is not supported.',
            get_class($paginator)
        );
        
        $this->assertSame($expectedMessage, $exception->getMessage());
    }
}
