<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView;

final class ExceptionTest extends TestCase
{
    public function testGetPaginator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The paginator is not set.');
        DataView\GridView::widget()->getPaginator();
    }

    public function testGetTranslator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The translator is not set.');
        DataView\GridView::widget()->getTranslator();
    }

    public function testGetUrlGenerator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Url generator is not set.');
        DataView\GridView::widget()->getUrlGenerator();
    }

    public function testPaginator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "paginator" property must be set.');
        DataView\GridView::widget()->render();
    }

    private function widgetBaseListView(): DataView\BaseListView
    {
        return new class () extends DataView\BaseListView {
            public function renderItems(): string
            {
                return '';
            }
        };
    }
}
