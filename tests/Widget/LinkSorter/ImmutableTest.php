<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\LinkSorter;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\Widget\LinkSorter;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    /**
     * @throws ReflectionException
     */
    public function testImmutable(): void
    {
        $linkSorter = LinkSorter::widget();
        $this->assertNotSame($linkSorter, $linkSorter->attribute(''));
        $this->assertNotSame($linkSorter, $linkSorter->attributes([]));
        $this->assertNotSame($linkSorter, $linkSorter->currentPage(0));
        $this->assertNotSame($linkSorter, $linkSorter->directions([]));
        $this->assertNotSame($linkSorter, $linkSorter->iconAsc(''));
        $this->assertNotSame($linkSorter, $linkSorter->iconAscClass(''));
        $this->assertNotSame($linkSorter, $linkSorter->iconDesc(''));
        $this->assertNotSame($linkSorter, $linkSorter->iconDescClass(''));
        $this->assertNotSame($linkSorter, $linkSorter->label(''));
        $this->assertNotSame($linkSorter, $linkSorter->linkAttributes([]));
        $this->assertNotSame($linkSorter, $linkSorter->pageConfig([]));
        $this->assertNotSame($linkSorter, $linkSorter->pageName(''));
        $this->assertNotSame($linkSorter, $linkSorter->pageSize(0));
        $this->assertNotSame($linkSorter, $linkSorter->pageSizeName(''));
        $this->assertNotSame($linkSorter, $linkSorter->urlArguments([]));
        $this->assertNotSame($linkSorter, $linkSorter->urlGenerator(Mock::UrlGenerator([])));
        $this->assertNotSame($linkSorter, $linkSorter->urlName(''));
        $this->assertNotSame($linkSorter, $linkSorter->urlQueryParameters([]));
    }
}
