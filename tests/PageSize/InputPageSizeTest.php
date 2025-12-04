<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\PageSize;

use LogicException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\PageSize\InputPageSize;
use Yiisoft\Yii\DataView\PageSize\PageSizeContext;

final class InputPageSizeTest extends TestCase
{
    public function testBase(): void
    {
        $html = (new InputPageSize())
            ->withContext(
                new PageSizeContext(
                    currentValue: 10,
                    defaultValue: 20,
                    constraint: false,
                    urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
                    defaultUrl: '/test',
                ),
            )
            ->render();

        $this->assertSame(
            '<input type="text" value="10" data-default-page-size="20" data-url-pattern="/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER" data-default-url="/test" onchange="window.location.href = this.value == this.dataset.defaultPageSize ? this.dataset.defaultUrl : this.dataset.urlPattern.replace(&quot;YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER&quot;, this.value)">',
            $html,
        );
    }

    public function testAddAttributes(): void
    {
        $html = (new InputPageSize())
            ->withContext(
                new PageSizeContext(
                    currentValue: 10,
                    defaultValue: 20,
                    constraint: false,
                    urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
                    defaultUrl: '/test',
                ),
            )
            ->attributes(['class' => 'form-control'])
            ->addAttributes(['id' => 'page-size-input'])
            ->render();

        $this->assertStringStartsWith(
            '<input type="text" id="page-size-input" class="form-control" value="10" ',
            $html,
        );
    }

    public function testAttributes(): void
    {
        $html = (new InputPageSize())
            ->withContext(
                new PageSizeContext(
                    currentValue: 10,
                    defaultValue: 20,
                    constraint: false,
                    urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
                    defaultUrl: '/test',
                ),
            )
            ->attributes(['class' => 'form-control'])
            ->attributes(['id' => 'page-size-input'])
            ->render();

        $this->assertStringStartsWith(
            '<input type="text" id="page-size-input" value="10" ',
            $html,
        );
    }

    public function testGetContextWithoutSettingContext(): void
    {
        $widget = new InputPageSize();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Context is not set.');
        $widget->render();
    }

    public function testImmutability(): void
    {
        $widget = new InputPageSize();

        $this->assertNotSame($widget, $widget->addAttributes([]));
        $this->assertNotSame($widget, $widget->attributes([]));
        $this->assertNotSame($widget, $widget->withContext(new PageSizeContext(10, 20, false, '/', '/')));
    }
}
