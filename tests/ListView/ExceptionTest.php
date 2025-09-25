<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\ListView;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Exception\DataReaderNotSetException;
use Yiisoft\Yii\DataView\ListView\ListView;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ExceptionTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John', 'age' => 20],
        ['id' => 2, 'name' => 'Mary', 'age' => 21],
    ];

    public function testGetPaginator(): void
    {
        $widget = ListView::widget();

        $this->expectException(DataReaderNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "dataReader" is not set.');
        $widget->render();
    }

    public function testNoItemContent(): void
    {
        $paginator = $this->createOffsetPaginator($this->data, 10);
        $widget = ListView::widget()->dataReader($paginator);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('"itemContent" must be set.');
        $widget->render();
    }

    public function testEmptyItemTag(): void
    {
        $widget = ListView::widget();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->itemTag('');
    }

    public function testEmptyListTag(): void
    {
        $widget = ListView::widget();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->listTag('');
    }

    public function testEmptySummaryTag(): void
    {
        $widget = ListView::widget();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->summaryTag('');
    }
}
