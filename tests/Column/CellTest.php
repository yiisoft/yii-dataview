<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column\Base;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Column\Base\Cell;

final class CellTest extends TestCase
{
    public function testDoubleEncodeReturnsNewInstanceWithModifiedValue(): void
    {
        $cell = new Cell();
        $newCell = $cell->doubleEncode(false);

        $this->assertNotSame($cell, $newCell);
        $this->assertFalse($this->getProperty($newCell, 'doubleEncode'));
        $this->assertTrue($this->getProperty($cell, 'doubleEncode'));
    }

    public function testAttributesReturnsNewInstanceWithAttributes(): void
    {
        $cell = new Cell();
        $newCell = $cell->attributes(['class' => 'test', 'data-id' => '123']);

        $this->assertNotSame($cell, $newCell);
        $this->assertSame(['class' => 'test', 'data-id' => '123'], $this->getProperty($newCell, 'attributes'));
        $this->assertSame([], $this->getProperty($cell, 'attributes'));
    }

    public function testAttributeReturnsNewInstanceWithSingleAttribute(): void
    {
        $cell = new Cell();
        $newCell = $cell->attribute('class', 'highlight');

        $this->assertNotSame($cell, $newCell);
        $this->assertSame(['class' => 'highlight'], $this->getProperty($newCell, 'attributes'));
        $this->assertSame([], $this->getProperty($cell, 'attributes'));
    }

    private function getProperty(object $object, string $property): mixed
    {
        $reflection = new \ReflectionClass($object);
        $prop = $reflection->getProperty($property);
        $prop->setAccessible(true);
        return $prop->getValue($object);
    }
}
