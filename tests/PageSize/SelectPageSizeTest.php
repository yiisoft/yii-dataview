<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\PageSize;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\PageSize\PageSizeContext;
use Yiisoft\Yii\DataView\PageSize\SelectPageSize;

final class SelectPageSizeTest extends TestCase
{
    public function testBase(): void
    {
        $html = (new SelectPageSize())
            ->withContext(
                new PageSizeContext(
                    currentValue: 10,
                    defaultValue: 20,
                    constraint: [10, 20, 50],
                    urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
                    defaultUrl: '/test',
                ),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <select data-default-page-size="20" data-url-pattern="/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER" data-default-url="/test" onchange="window.location.href = this.value == this.dataset.defaultPageSize ? this.dataset.defaultUrl : this.dataset.urlPattern.replace(&quot;YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER&quot;, this.value)">
            <option value="10" selected>10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            </select>
            HTML,
            $html,
        );
    }

    #[TestWith([true])]
    #[TestWith([false])]
    #[TestWith([10])]
    #[TestWith([[10]])]
    public function testConstraintWithoutRender(mixed $constraint): void
    {
        $html = (new SelectPageSize())
            ->withContext(
                new PageSizeContext(
                    currentValue: 10,
                    defaultValue: 20,
                    constraint: $constraint,
                    urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
                    defaultUrl: '/test',
                ),
            )
            ->render();

        $this->assertSame('', $html);
    }

    public function testAddAttributes(): void
    {
        $html = (new SelectPageSize())
            ->withContext(
                new PageSizeContext(
                    currentValue: 10,
                    defaultValue: 20,
                    constraint: [10, 20, 50],
                    urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
                    defaultUrl: '/test',
                ),
            )
            ->attributes(['class' => 'form-select'])
            ->addAttributes(['id' => 'page-size-select'])
            ->render();

        $this->assertStringStartsWith(
            '<select id="page-size-select" class="form-select" data-default-page-size="20" ',
            $html,
        );
    }

    public function testAttributes(): void
    {
        $html = (new SelectPageSize())
            ->withContext(
                new PageSizeContext(
                    currentValue: 10,
                    defaultValue: 20,
                    constraint: [10, 20, 50],
                    urlPattern: '/test?pagesize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER',
                    defaultUrl: '/test',
                ),
            )
            ->attributes(['class' => 'form-select'])
            ->attributes(['id' => 'page-size-select'])
            ->render();

        $this->assertStringStartsWith(
            '<select id="page-size-select" data-default-page-size="20" ',
            $html,
        );
    }

    public function testImmutability(): void
    {
        $widget = new SelectPageSize();

        $this->assertNotSame($widget, $widget->addAttributes([]));
        $this->assertNotSame($widget, $widget->attributes([]));
        $this->assertNotSame($widget, $widget->withContext(new PageSizeContext(10, 20, [10, 20], '/', '/')));
    }
}
