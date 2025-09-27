<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Filter\Factory;

use PHPUnit\Framework\TestCase;
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

        $this->assertSame('name', $filter->field);
        $this->assertSame('John', $filter->value);
    }
}
