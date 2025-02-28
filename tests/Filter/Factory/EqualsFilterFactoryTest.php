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
        $filter = $factory->create('name', 'John');

        $this->assertInstanceOf(Equals::class, $filter);
        
        // Test the filter works as expected
        $this->assertSame('name', $filter->getField());
        $this->assertSame('John', $filter->getValue());
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

        // PHP's empty() function returns true for '0', so this should return null
        $this->assertNull($filter);
    }
    
    public function testCreateWithNonEmptyNumericValue(): void
    {
        $factory = new EqualsFilterFactory();
        $filter = $factory->create('age', '25');

        $this->assertInstanceOf(Equals::class, $filter);
        $this->assertSame('age', $filter->getField());
        $this->assertSame('25', $filter->getValue());
    }
}
