<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Filter\Factory;

use PHPUnit\Framework\Attributes\DataProvider;
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

        $this->assertSame('name', $filter->getField());
        $this->assertSame('John', $filter->getValue());
        $this->assertNull($filter->isCaseSensitive());
    }

    public function testCreateWithEmptyValue(): void
    {
        $factory = new LikeFilterFactory();
        $filter = $factory->create('name', '');

        $this->assertNull($filter);
    }

    public function testCreateWithZeroValue(): void
    {
        $factory = new LikeFilterFactory();
        $filter = $factory->create('name', '0');

        $this->assertNull($filter);
    }

    public static function dataCaseSensitive(): iterable
    {
        yield [null];
        yield [true];
        yield [false];
    }

    #[DataProvider('dataCaseSensitive')]
    public function testCaseSensitive(?bool $value): void
    {
        $factory = new LikeFilterFactory($value);

        $filter = $factory->create('name', 'John');

        $this->assertSame($value, $filter->isCaseSensitive());
    }
}
