<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use PHPUnit\Framework\TestCase;

use function is_array;
use function iterator_to_array;

/**
 * @psalm-require-extends TestCase
 */
trait AssertTrait
{
    /**
     * @psalm-param list<array> $expected
     */
    public function assertSameItems(array $expected, iterable $actual, string $message = ''): void
    {
        static::assertSame(
            $expected,
            array_values(is_array($actual) ? $actual : iterator_to_array($actual)),
            $message,
        );
    }
}
