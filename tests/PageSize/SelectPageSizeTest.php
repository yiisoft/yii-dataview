<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\PageSize;

use LogicException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\PageSize\PageSizeContext;
use Yiisoft\Yii\DataView\PageSize\SelectPageSize;

final class SelectPageSizeTest extends TestCase
{
    public function testRenderWithArrayConstraint(): void
    {
        $context = new PageSizeContext(
            currentValue: 10,
            defaultValue: 20,
            constraint: [10, 20, 50, 100],
            urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
            defaultUrl: '/test'
        );

        $widget = new SelectPageSize();
        $widget = $widget->withContext($context);

        $html = $widget->render();

        $this->assertStringContainsString('<option value="10" selected>', $html);
        $this->assertStringContainsString('<option value="20">', $html);
        $this->assertStringContainsString('<option value="50">', $html);
        $this->assertStringContainsString('<option value="100">', $html);
        $this->assertStringContainsString('data-default-page-size="20"', $html);
        $this->assertStringContainsString('data-url-pattern="/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER"', $html);
        $this->assertStringContainsString('data-default-url="/test"', $html);
        $this->assertStringContainsString('onchange=', $html);
    }

    public function testRenderWithInsufficientOptions(): void
    {
        // Test with a single option array (should return empty string)
        $context = new PageSizeContext(
            currentValue: 10,
            defaultValue: 10,
            constraint: [10],
            urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
            defaultUrl: '/test'
        );

        $widget = new SelectPageSize();
        $widget = $widget->withContext($context);

        $html = $widget->render();
        $this->assertSame('', $html);

        // Test with non-array constraint (should return empty string)
        $context = new PageSizeContext(
            currentValue: 10,
            defaultValue: 10,
            constraint: true,
            urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
            defaultUrl: '/test'
        );

        $widget = $widget->withContext($context);
        $html = $widget->render();
        $this->assertSame('', $html);
    }

    public function testAddAttributes(): void
    {
        $context = new PageSizeContext(
            currentValue: 10,
            defaultValue: 20,
            constraint: [10, 20, 50],
            urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
            defaultUrl: '/test'
        );

        $widget = new SelectPageSize();
        $widget = $widget->withContext($context);
        $widget = $widget->addAttributes(['class' => 'form-select', 'id' => 'page-size-select']);

        $html = $widget->render();

        $this->assertStringContainsString('class="form-select"', $html);
        $this->assertStringContainsString('id="page-size-select"', $html);
    }

    public function testAttributes(): void
    {
        $context = new PageSizeContext(
            currentValue: 10,
            defaultValue: 20,
            constraint: [10, 20, 50],
            urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
            defaultUrl: '/test'
        );

        $widget = new SelectPageSize();
        $widget = $widget->withContext($context);
        $widget = $widget->attributes(['class' => 'custom-select', 'data-test' => 'value']);

        $html = $widget->render();

        $this->assertStringContainsString('class="custom-select"', $html);
        $this->assertStringContainsString('data-test="value"', $html);
    }
}
