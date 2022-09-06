<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView;
use Yiisoft\Yii\DataView\Exception;

final class ExceptionTest extends TestCase
{
    public function testGetPaginator(): void
    {
        $this->expectException(Exception\PaginatorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "paginator" is not set.');
        DataView\GridView::widget()->getPaginator();
    }

    public function testGetTranslator(): void
    {
        $this->expectException(Exception\TranslatorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "translator" is not set.');
        DataView\GridView::widget()->getTranslator();
    }

    public function testGetUrlGenerator(): void
    {
        $this->expectException(Exception\UrlGeneratorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "urlgenerator" is not set.');
        DataView\GridView::widget()->getUrlGenerator();
    }

    public function testPaginator(): void
    {
        $this->expectException(Exception\PaginatorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "paginator" is not set.');
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
