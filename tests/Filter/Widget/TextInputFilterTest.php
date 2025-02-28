<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Filter\Widget;

use PHPUnit\Framework\TestCase;
use Yiisoft\Html\Tag\Input;
use Yiisoft\Yii\DataView\Filter\Widget\Context;
use Yiisoft\Yii\DataView\Filter\Widget\TextInputFilter;

/**
 * @covers \Yiisoft\Yii\DataView\Filter\Widget\TextInputFilter
 */
final class TextInputFilterTest extends TestCase
{
    public function testRenderFilter(): void
    {
        $filter = new TextInputFilter();
        $context = new Context('username', 'john', 'filter-form');
        
        $result = $filter->renderFilter($context);
        
        $this->assertStringContainsString('name="username"', $result);
        $this->assertStringContainsString('value="john"', $result);
        $this->assertStringContainsString('form="filter-form"', $result);
        $this->assertStringContainsString('type="text"', $result);
    }
    
    public function testRenderWithNullValue(): void
    {
        $filter = new TextInputFilter();
        $context = new Context('username', null, 'filter-form');
        
        $result = $filter->renderFilter($context);
        
        $this->assertStringContainsString('name="username"', $result);
        $this->assertStringNotContainsString('value=', $result);
        $this->assertStringContainsString('form="filter-form"', $result);
    }
    
    public function testAddAttributes(): void
    {
        $filter = new TextInputFilter();
        $newFilter = $filter->addAttributes(['class' => 'form-control', 'placeholder' => 'Enter username']);
        
        $this->assertNotSame($filter, $newFilter);
        
        $context = new Context('username', 'john', 'filter-form');
        $result = $newFilter->renderFilter($context);
        
        $this->assertStringContainsString('class="form-control"', $result);
        $this->assertStringContainsString('placeholder="Enter username"', $result);
    }
    
    public function testAttributes(): void
    {
        $filter = new TextInputFilter();
        $filter = $filter->addAttributes(['data-test' => 'original']);
        
        $newFilter = $filter->attributes(['class' => 'form-control', 'id' => 'username-filter']);
        
        $this->assertNotSame($filter, $newFilter);
        
        $context = new Context('username', 'john', 'filter-form');
        $result = $newFilter->renderFilter($context);
        
        $this->assertStringContainsString('class="form-control"', $result);
        $this->assertStringContainsString('id="username-filter"', $result);
        $this->assertStringNotContainsString('data-test="original"', $result);
    }
    
    public function testRender(): void
    {
        $filter = new TextInputFilter();
        $context = new Context('username', 'john', 'filter-form');
        
        // Set context using withContext
        $filter = $filter->withContext($context);
        
        // Test render method
        $result = $filter->render();
        
        $this->assertStringContainsString('name="username"', $result);
        $this->assertStringContainsString('value="john"', $result);
        $this->assertStringContainsString('form="filter-form"', $result);
    }
}
