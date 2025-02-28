<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Filter\Widget;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Filter\Widget\Context;
use Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter;

/**
 * @covers \Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter
 * @covers \Yiisoft\Yii\DataView\Filter\Widget\Context
 * @covers \Yiisoft\Yii\DataView\Filter\Widget\FilterWidget
 */
final class DropdownFilterTest extends TestCase
{
    public function testRenderFilter(): void
    {
        $filter = new DropdownFilter();
        $filter = $filter->optionsData([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ]);
        $context = new Context('status', 'active', 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertStringContainsString('name="status"', $html);
        $this->assertStringContainsString('form="filter-form"', $html);
        $this->assertStringContainsString('onChange="this.form.submit()"', $html);
        $this->assertStringContainsString('value="active"', $html);
        $this->assertStringContainsString('selected', $html);
    }

    public function testRenderFilterWithoutValue(): void
    {
        $filter = new DropdownFilter();
        $context = new Context('status', null, 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertStringContainsString('name="status"', $html);
        $this->assertStringContainsString('form="filter-form"', $html);
        $this->assertStringContainsString('onChange="this.form.submit()"', $html);
        $this->assertStringNotContainsString('value="', $html);
    }

    public function testOptionsData(): void
    {
        $filter = new DropdownFilter();
        $options = [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ];

        $filter = $filter->optionsData($options);
        $context = new Context('status', null, 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertStringContainsString('>Active<', $html);
        $this->assertStringContainsString('>Inactive<', $html);
        $this->assertStringContainsString('value="active"', $html);
        $this->assertStringContainsString('value="inactive"', $html);
    }

    public function testAddAttributes(): void
    {
        $filter = new DropdownFilter();
        $filter = $filter->addAttributes(['class' => 'custom-select', 'data-test' => 'value']);

        $context = new Context('status', null, 'filter-form');
        $html = $filter->renderFilter($context);

        $this->assertStringContainsString('class="custom-select"', $html);
        $this->assertStringContainsString('data-test="value"', $html);
    }

    public function testAttributes(): void
    {
        $filter = new DropdownFilter();
        $filter = $filter->attributes(['class' => 'new-select', 'required' => true]);

        $context = new Context('status', null, 'filter-form');
        $html = $filter->renderFilter($context);

        $this->assertStringContainsString('class="new-select"', $html);
        $this->assertStringContainsString('required', $html);
    }
}
