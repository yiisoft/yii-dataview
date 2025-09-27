<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView\Column\Base;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\GridView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Tests\Support\StringableObject;

final class CellTest extends TestCase
{
    public function testDefaults(): void
    {
        $cell = new Cell();

        $this->assertSame([], $cell->getAttributes());
        $this->assertNull($cell->shouldEncode());
        $this->assertTrue($cell->shouldDoubleEncode());
        $this->assertSame([], $cell->getContent());
        $this->assertTrue($cell->isEmptyContent());
    }

    public function testEncode(): void
    {
        $cell = (new Cell())->encode(true);
        $this->assertTrue($cell->shouldEncode());
    }

    public function testDoubleEncode(): void
    {
        $cell = (new Cell())->doubleEncode(true);
        $this->assertTrue($cell->shouldDoubleEncode());
    }

    public function testContent(): void
    {
        $cell = (new Cell())->content('test1', 'test2');
        $this->assertSame(['test1', 'test2'], $cell->getContent());
    }

    public function testAttributes(): void
    {
        $cell = new Cell();

        $cell = $cell->attributes(['data-test' => 'value']);
        $this->assertSame(['data-test' => 'value'], $cell->getAttributes());

        $cell = $cell->attributes(['class' => 'test']);
        $this->assertSame(['class' => 'test'], $cell->getAttributes());

        $cell = $cell->addAttributes(['id' => 'X']);
        $this->assertSame(['class' => 'test', 'id' => 'X'], $cell->getAttributes());
    }

    public function testAddClass(): void
    {
        $cell = new Cell();

        $cell = $cell->addClass('red');
        $this->assertSame(['class' => 'red'], $cell->getAttributes());

        $cell = $cell->addClass('bold');
        $this->assertSame(['class' => 'red bold'], $cell->getAttributes());
    }

    public static function dataIsEmptyContent(): iterable
    {
        yield [true, ['']];
        yield [true, [new StringableObject('')]];
        yield [false, ['test']];
    }

    #[DataProvider('dataIsEmptyContent')]
    public function testIsEmptyContent(bool $expected, array $content): void
    {
        $cell = (new Cell())->content(...$content);
        $this->assertSame($expected, $cell->isEmptyContent());
    }

    public function testImmutability(): void
    {
        $cell = new Cell();
        $this->assertNotSame($cell, $cell->encode(true));
        $this->assertNotSame($cell, $cell->doubleEncode(false));
        $this->assertNotSame($cell, $cell->content('test'));
        $this->assertNotSame($cell, $cell->addAttributes([]));
        $this->assertNotSame($cell, $cell->attributes([]));
        $this->assertNotSame($cell, $cell->attribute('test', 'value'));
        $this->assertNotSame($cell, $cell->addClass('test'));
    }
}
