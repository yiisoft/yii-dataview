<?php

declare(strict_types=1);

namespace DetailView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\DetailView\DetailView;
use Yiisoft\Yii\DataView\ValuePresenter\SimpleValuePresenter;

final class DetailViewTest extends TestCase
{
    public function testImmutability(): void
    {
        $widget = DetailView::widget();
        $this->assertNotSame($widget, $widget->data([]));
        $this->assertNotSame($widget, $widget->fields());
        $this->assertNotSame($widget, $widget->containerTag(null));
        $this->assertNotSame($widget, $widget->containerAttributes([]));
        $this->assertNotSame($widget, $widget->prepend());
        $this->assertNotSame($widget, $widget->append());
        $this->assertNotSame($widget, $widget->listTag(null));
        $this->assertNotSame($widget, $widget->listAttributes([]));
        $this->assertNotSame($widget, $widget->fieldTag(null));
        $this->assertNotSame($widget, $widget->fieldAttributes([]));
        $this->assertNotSame($widget, $widget->fieldPrepend());
        $this->assertNotSame($widget, $widget->fieldAppend());
        $this->assertNotSame($widget, $widget->fieldTemplate(''));
        $this->assertNotSame($widget, $widget->labelTag(null));
        $this->assertNotSame($widget, $widget->labelAttributes([]));
        $this->assertNotSame($widget, $widget->labelPrepend());
        $this->assertNotSame($widget, $widget->labelAppend());
        $this->assertNotSame($widget, $widget->valueTag(null));
        $this->assertNotSame($widget, $widget->valueAttributes([]));
        $this->assertNotSame($widget, $widget->valuePrepend());
        $this->assertNotSame($widget, $widget->valueAppend());
        $this->assertNotSame($widget, $widget->valuePresenter(new SimpleValuePresenter()));
    }
}
