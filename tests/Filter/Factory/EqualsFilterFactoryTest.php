<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Filter\Factory;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Yii\DataView\Filter\Factory\EqualsFilterFactory;

/**
 * @covers \Yiisoft\Yii\DataView\Filter\Factory\EqualsFilterFactory
 */
final class EqualsFilterFactoryTest extends TestCase
{
    public function testCreateWithValidValue(): void
    {
        $factory = new EqualsFilterFactory();

        /**@var Equals $filter */
        $filter = $factory->create('name', 'John');

        $this->assertInstanceOf(Equals::class, $filter);

        $this->assertSame('name', $filter->field);
        $this->assertSame('John', $filter->value);
    }

    public function testCreateWithEmptyValue(): void
    {
        $factory = new EqualsFilterFactory();
        $filter = $factory->create('name', '');

        $this->assertNull($filter);
    }

    public function testCreateWithZeroValue(): void
    {
        $factory = new EqualsFilterFactory();
        $filter = $factory->create('age', '0');

        $this->assertNull($filter);
    }

    public function testCreateWithNonEmptyNumericValue(): void
    {
        $factory = new EqualsFilterFactory();

        /** @var Equals $filter */
        $filter = $factory->create('quantity', '42');

        $this->assertInstanceOf(Equals::class, $filter);

        $this->assertSame('quantity', $filter->field);
        $this->assertSame('42', $filter->value);
    }
}
