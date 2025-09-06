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

    public function testIsVisibleDefault(): void
    {
        $field = new DataField(value: 'test');
        $this->assertTrue($field->visible);
    }

    public function testIsVisibleTrue(): void
    {
        $field = new DataField(value: 'test', visible: true);
        $this->assertTrue($field->visible);
    }

    public function testIsVisibleFalse(): void
    {
        $field = new DataField(value: 'test', visible: false);
        $this->assertFalse($field->visible);
    }

    public function testVisibilityInDetailView(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30, 'email' => 'john@example.com'])
            ->fields(
                new DataField(property: 'name', label: 'Full Name'),
                new DataField(property: 'age', label: 'Age', visible: false),
                new DataField(property: 'email', label: 'Email'),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt>Full Name</dt>
            <dd>John</dd>
            <dt>Email</dt>
            <dd>john@example.com</dd>
            </dl>
            HTML,
            $html,
        );

        // Verify that the age field is not present in the output
        $this->assertStringNotContainsString('Age', $html);
        $this->assertStringNotContainsString('30', $html);
    }
}
