<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\DetailView;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ExceptionTest extends TestCase
{
    use TestTrait;

    public function testColumnsWithoutAttributes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "attribute" must be set.');
        DetailView::widget()
            ->columns([['label' => 'id']])
            ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
            ->render();
    }

    public function testColumnsWithAttributesNotString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "attribute" must be a string.');
        DetailView::widget()
            ->columns([['attribute' => 1]])
            ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
            ->render();
    }

    public function testColumnsWithLabelNotString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "label" must be a string.');
        DetailView::widget()
            ->columns([['attribute' => 'id', 'label' => 1]])
            ->data(['id' => 1, 'username' => 'tests 1', 'total' => '10'])
            ->render();
    }

    public function testDataEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "data" must be set.');
        DetailView::widget()->columns([['attribute' => 'id']])->data([])->render();
    }
}
