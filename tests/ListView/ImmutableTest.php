<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\ListView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\ListView;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testInmutable(): void
    {
        $listView = ListView::widget();
        $this->assertNotSame($listView, $listView->afterItem(fn () => ''));
        $this->assertNotSame($listView, $listView->beforeItem(fn () => ''));
        $this->assertNotSame($listView, $listView->itemView(''));
        $this->assertNotSame($listView, $listView->itemViewAttributes([]));
        $this->assertNotSame($listView, $listView->separator(''));
        $this->assertNotSame($listView, $listView->viewParams([]));
        $this->assertNotSame($listView, $listView->webView(Mock::webView()));
    }
}
