<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Url;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Url\UrlConfig;
use Yiisoft\Yii\DataView\Url\UrlParameterType;

final class UrlConfigTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $urlConfig = new UrlConfig();

        $this->assertSame('page', $urlConfig->getPageParameterName());
        $this->assertSame('prev-page', $urlConfig->getPreviousPageParameterName());
        $this->assertSame('pagesize', $urlConfig->getPageSizeParameterName());
        $this->assertSame('sort', $urlConfig->getSortParameterName());
        $this->assertSame(UrlParameterType::Query, $urlConfig->getPageParameterType());
        $this->assertSame(UrlParameterType::Query, $urlConfig->getPreviousPageParameterType());
        $this->assertSame(UrlParameterType::Query, $urlConfig->getPageSizeParameterType());
        $this->assertSame(UrlParameterType::Query, $urlConfig->getSortParameterType());
        $this->assertSame([], $urlConfig->getArguments());
        $this->assertSame([], $urlConfig->getQueryParameters());
    }

    public function testWithPageParameterName(): void
    {
        $urlConfig = new UrlConfig();
        $new = $urlConfig->withPageParameterName('p');

        $this->assertNotSame($urlConfig, $new);
        $this->assertSame('page', $urlConfig->getPageParameterName());
        $this->assertSame('p', $new->getPageParameterName());
    }

    public function testWithPreviousPageParameterName(): void
    {
        $urlConfig = new UrlConfig();
        $new = $urlConfig->withPreviousPageParameterName('prev');

        $this->assertNotSame($urlConfig, $new);
        $this->assertSame('prev-page', $urlConfig->getPreviousPageParameterName());
        $this->assertSame('prev', $new->getPreviousPageParameterName());
    }

    public function testWithPageSizeParameterName(): void
    {
        $urlConfig = new UrlConfig();
        $new = $urlConfig->withPageSizeParameterName('size');

        $this->assertNotSame($urlConfig, $new);
        $this->assertSame('pagesize', $urlConfig->getPageSizeParameterName());
        $this->assertSame('size', $new->getPageSizeParameterName());
    }

    public function testWithSortParameterName(): void
    {
        $urlConfig = new UrlConfig();
        $new = $urlConfig->withSortParameterName('order');

        $this->assertNotSame($urlConfig, $new);
        $this->assertSame('sort', $urlConfig->getSortParameterName());
        $this->assertSame('order', $new->getSortParameterName());
    }

    public function testWithPageParameterType(): void
    {
        $urlConfig = new UrlConfig();
        $new = $urlConfig->withPageParameterType(UrlParameterType::Path);

        $this->assertNotSame($urlConfig, $new);
        $this->assertSame(UrlParameterType::Query, $urlConfig->getPageParameterType());
        $this->assertSame(UrlParameterType::Path, $new->getPageParameterType());
    }

    public function testWithPreviousPageParameterType(): void
    {
        $urlConfig = new UrlConfig();
        $new = $urlConfig->withPreviousPageParameterType(UrlParameterType::Path);

        $this->assertNotSame($urlConfig, $new);
        $this->assertSame(UrlParameterType::Query, $urlConfig->getPreviousPageParameterType());
        $this->assertSame(UrlParameterType::Path, $new->getPreviousPageParameterType());
    }

    public function testWithPageSizeParameterType(): void
    {
        $urlConfig = new UrlConfig();
        $new = $urlConfig->withPageSizeParameterType(UrlParameterType::Path);

        $this->assertNotSame($urlConfig, $new);
        $this->assertSame(UrlParameterType::Query, $urlConfig->getPageSizeParameterType());
        $this->assertSame(UrlParameterType::Path, $new->getPageSizeParameterType());
    }

    public function testWithSortParameterType(): void
    {
        $urlConfig = new UrlConfig();
        $new = $urlConfig->withSortParameterType(UrlParameterType::Path);

        $this->assertNotSame($urlConfig, $new);
        $this->assertSame(UrlParameterType::Query, $urlConfig->getSortParameterType());
        $this->assertSame(UrlParameterType::Path, $new->getSortParameterType());
    }

    public function testWithArguments(): void
    {
        $urlConfig = new UrlConfig();
        $new = $urlConfig->withArguments(['foo' => 'bar']);

        $this->assertNotSame($urlConfig, $new);
        $this->assertSame([], $urlConfig->getArguments());
        $this->assertSame(['foo' => 'bar'], $new->getArguments());
    }

    public function testWithQueryParameters(): void
    {
        $urlConfig = new UrlConfig();
        $new = $urlConfig->withQueryParameters(['key' => 'value']);

        $this->assertNotSame($urlConfig, $new);
        $this->assertSame([], $urlConfig->getQueryParameters());
        $this->assertSame(['key' => 'value'], $new->getQueryParameters());
    }
}
