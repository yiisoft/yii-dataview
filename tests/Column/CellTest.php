<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column\Base;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Column\Base\Cell;

final class CellTest extends TestCase
{
    public function testDoubleEncodeReturnsModifiedInstance(): void
    {
        $cell = new Cell();
        $new = $cell->doubleEncode(false);

        $this->assertNotSame($cell, $new);
        $this->assertSame(
            $this->renderContent($cell),
            $this->renderContent($new)
        );
    }

    public function testEncodeReturnsModifiedInstance(): void
    {
        $cell = new Cell();
        $new = $cell->encode(false);

        $this->assertNotSame($cell, $new);
        $this->assertSame(
            $this->renderContent($cell),
            $this->renderContent($new)
        );
    }

    public function testAttributesReturnsModifiedInstance(): void
    {
        $cell = new Cell();
        $new = $cell->attributes(['class' => 'red']);

        $this->assertNotSame($cell, $new);
        $this->assertSame(
            $this->renderContent($cell),
            $this->renderContent($new)
        );
    }

    public function testAttributeReturnsModifiedInstance(): void
    {
        $cell = new Cell();
        $new = $cell->attribute('data-id', '123');

        $this->assertNotSame($cell, $new);
        $this->assertSame(
            $this->renderContent($cell),
            $this->renderContent($new)
        );
    }

    public function testContentReturnsModifiedInstanceWithNewContent(): void
    {
        $cell = new Cell();
        $new = $cell->content('Hello', 'World');

        $this->assertNotSame($cell, $new);
        $this->assertSame(['Hello', 'World'], $new->getContent());
    }

    public function testIsEmptyContentReturnsTrueOnlyIfNoContent(): void
    {
        $cell = new Cell();
        $this->assertTrue($cell->isEmptyContent());

        $cellWithContent = $cell->content('Hello');
        $this->assertFalse($cellWithContent->isEmptyContent());
    }

    private function renderContent(Cell $cell): string
    {
        return implode('', $cell->getContent());
    }
}
