<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Filter\Widget;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Filter\Widget\Context;
use Yiisoft\Yii\DataView\Filter\Widget\TextInputFilter;

/**
 * @covers \Yiisoft\Yii\DataView\Filter\Widget\TextInputFilter
 * @covers \Yiisoft\Yii\DataView\Filter\Widget\Context
 * @covers \Yiisoft\Yii\DataView\Filter\Widget\FilterWidget
 */
final class TextInputFilterTest extends TestCase
{
    public function testBase(): void
    {
        $filter = TextInputFilter::widget();
        $context = new Context('username', 'john', 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertSame(
            '<input type="text" name="username" value="john" form="filter-form">',
            $html,
        );
    }

    public function testWithNullValue(): void
    {
        $filter = TextInputFilter::widget();
        $context = new Context('username', null, 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertSame(
            '<input type="text" name="username" form="filter-form">',
            $html,
        );
    }

    public function testAddAttributes(): void
    {
        $filter = TextInputFilter::widget()
            ->addAttributes(['class' => 'form-control'])
            ->addAttributes(['placeholder' => 'Enter username']);
        $context = new Context('username', 'john', 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertSame(
            '<input type="text" class="form-control" name="username" value="john" form="filter-form" placeholder="Enter username">',
            $html,
        );
    }

    public function testAttributes(): void
    {
        $filter = TextInputFilter::widget()
            ->attributes(['data-test' => 'original'])
            ->attributes(['class' => 'form-control', 'id' => 'username-filter']);
        $context = new Context('username', 'john', 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertSame(
            '<input type="text" id="username-filter" class="form-control" name="username" value="john" form="filter-form">',
            $html,
        );
    }

    public function testImmutability(): void
    {
        $filter = TextInputFilter::widget();
        $this->assertNotSame($filter, $filter->addAttributes([]));
        $this->assertNotSame($filter, $filter->attributes([]));
    }
}
