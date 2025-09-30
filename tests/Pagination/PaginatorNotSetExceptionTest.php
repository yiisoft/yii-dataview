<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Pagination\PaginatorNotSetException;

final class PaginatorNotSetExceptionTest extends TestCase
{
    public function testDefaultMessage(): void
    {
        $exception = new PaginatorNotSetException();

        $this->assertSame(
            'Failed to create widget because "paginator" is not set.',
            $exception->getMessage(),
        );
    }

    public function testCustomMessage(): void
    {
        $customMessage = 'Custom error message for paginator';
        $exception = new PaginatorNotSetException($customMessage);

        $this->assertSame($customMessage, $exception->getMessage());
    }

    public function testEmptyMessageUsesDefault(): void
    {
        $exception = new PaginatorNotSetException('');

        $this->assertSame(
            'Failed to create widget because "paginator" is not set.',
            $exception->getMessage(),
        );
    }

    public function testGetName(): void
    {
        $exception = new PaginatorNotSetException();

        $this->assertSame(
            'Failed to create widget because "paginator" is not set.',
            $exception->getName(),
        );
    }

    public function testGetSolution(): void
    {
        $exception = new PaginatorNotSetException();

        $solution = $exception->getSolution();

        $this->assertIsString($solution);
        $this->assertStringContainsString('You can configure the', $solution);
    }
}
