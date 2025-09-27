<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Filter\Factory;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory;

/**
 * @covers \Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory
 */
final class LikeFilterFactoryTest extends TestCase
{
    public function testBase(): void
    {
        $factory = new LikeFilterFactory();

        $filter = $factory->create('name', 'John');

        $this->assertSame('name', $filter->field);
        $this->assertSame('John', $filter->value);
        $this->assertNull($filter->caseSensitive);
    }

    #[TestWith([null])]
    #[TestWith([true])]
    #[TestWith([false])]
    public function testCaseSensitive(?bool $value): void
    {
        $factory = new LikeFilterFactory($value);

        $filter = $factory->create('name', 'John');

        $this->assertSame($value, $filter->caseSensitive);
    }
}
