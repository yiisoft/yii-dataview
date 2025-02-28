<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\PageSize;

use LogicException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\PageSize\InputPageSize;
use Yiisoft\Yii\DataView\PageSize\PageSizeContext;

final class InputPageSizeTest extends TestCase
{
    public function testRenderWithContext(): void
    {
        $context = new PageSizeContext(
            currentValue: 10,
            defaultValue: 20,
            constraint: false,
            urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
            defaultUrl: '/test'
        );

        $widget = new InputPageSize();
        $widget = $widget->withContext($context);

        $html = $widget->render();

        $this->assertStringContainsString('value="10"', $html);
        $this->assertStringContainsString('data-default-page-size="20"', $html);
        $this->assertStringContainsString('data-url-pattern="/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER"', $html);
        $this->assertStringContainsString('data-default-url="/test"', $html);
        $this->assertStringContainsString('onchange=', $html);
    }

    public function testAddAttributes(): void
    {
        $context = new PageSizeContext(
            currentValue: 10,
            defaultValue: 20,
            constraint: false,
            urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
            defaultUrl: '/test'
        );

        $widget = new InputPageSize();
        $widget = $widget->withContext($context);
        $widget = $widget->addAttributes(['class' => 'form-control', 'id' => 'page-size-input']);

        $html = $widget->render();

        $this->assertStringContainsString('class="form-control"', $html);
        $this->assertStringContainsString('id="page-size-input"', $html);
    }

    public function testAttributes(): void
    {
        $context = new PageSizeContext(
            currentValue: 10,
            defaultValue: 20,
            constraint: false,
            urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
            defaultUrl: '/test'
        );

        $widget = new InputPageSize();
        $widget = $widget->withContext($context);
        $widget = $widget->attributes(['class' => 'custom-input', 'data-test' => 'value']);

        $html = $widget->render();

        $this->assertStringContainsString('class="custom-input"', $html);
        $this->assertStringContainsString('data-test="value"', $html);
    }

    public function testGetContextWithoutSettingContext(): void
    {
        $widget = new InputPageSize();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Context is not set.');

        // This will trigger the getContext() method internally
        $widget->render();
    }
}
