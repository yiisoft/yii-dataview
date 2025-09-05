<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\DetailView;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\DetailView\DataField;
use Yiisoft\Yii\DataView\DetailView\DetailView;

final class DataFieldTest extends TestCase
{
    public function testBase(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->fields(
                new DataField(value: '-'),
                new DataField(property: 'name', label: 'Full Name'),
                new DataField(property: 'age', label: 'Age'),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt></dt>
            <dd>-</dd>
            <dt>Full Name</dt>
            <dd>John</dd>
            <dt>Age</dt>
            <dd>30</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithoutPropertyAndValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Either "property" or "value" must be set.');
        new DataField();
    }
}
