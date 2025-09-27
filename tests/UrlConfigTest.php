<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests;

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
        $this->assertSame(UrlParameterType::QUERY, $urlConfig->getPageParameterType());
        $this->assertSame(UrlParameterType::QUERY, $urlConfig->getPreviousPageParameterType());
        $this->assertSame(UrlParameterType::QUERY, $urlConfig->getPageSizeParameterType());
        $this->assertSame(UrlParameterType::QUERY, $urlConfig->getSortParameterType());
        $this->assertSame([], $urlConfig->getArguments());
        $this->assertSame([], $urlConfig->getQueryParameters());
    }

    public function testWithMethods(): void
    {
        $urlConfig = new UrlConfig();

        $newConfig = $urlConfig->withPageParameterName('p');
        $this->assertNotSame($urlConfig, $newConfig);
        $this->assertSame('p', $newConfig->getPageParameterName());

        $newConfig = $urlConfig->withPreviousPageParameterName('pp');
        $this->assertNotSame($urlConfig, $newConfig);
        $this->assertSame('pp', $newConfig->getPreviousPageParameterName());

        $newConfig = $urlConfig->withPageSizeParameterName('ps');
        $this->assertNotSame($urlConfig, $newConfig);
        $this->assertSame('ps', $newConfig->getPageSizeParameterName());

        $newConfig = $urlConfig->withSortParameterName('s');
        $this->assertNotSame($urlConfig, $newConfig);
        $this->assertSame('s', $newConfig->getSortParameterName());

        $newConfig = $urlConfig->withPageParameterType(UrlParameterType::PATH);
        $this->assertNotSame($urlConfig, $newConfig);
        $this->assertSame(UrlParameterType::PATH, $newConfig->getPageParameterType());

        $newConfig = $urlConfig->withPreviousPageParameterType(UrlParameterType::PATH);
        $this->assertNotSame($urlConfig, $newConfig);
        $this->assertSame(UrlParameterType::PATH, $newConfig->getPreviousPageParameterType());

        $newConfig = $urlConfig->withPageSizeParameterType(UrlParameterType::PATH);
        $this->assertNotSame($urlConfig, $newConfig);
        $this->assertSame(UrlParameterType::PATH, $newConfig->getPageSizeParameterType());

        $newConfig = $urlConfig->withSortParameterType(UrlParameterType::PATH);
        $this->assertNotSame($urlConfig, $newConfig);
        $this->assertSame(UrlParameterType::PATH, $newConfig->getSortParameterType());

        $arguments = ['controller' => 'site', 'action' => 'index'];
        $newConfig = $urlConfig->withArguments($arguments);
        $this->assertNotSame($urlConfig, $newConfig);
        $this->assertSame($arguments, $newConfig->getArguments());

        $queryParams = ['q' => 'search', 'filter' => 'active'];
        $newConfig = $urlConfig->withQueryParameters($queryParams);
        $this->assertNotSame($urlConfig, $newConfig);
        $this->assertSame($queryParams, $newConfig->getQueryParameters());
    }

    public function testChaining(): void
    {
        $urlConfig = new UrlConfig();

        $newConfig = $urlConfig
            ->withPageParameterName('p')
            ->withPageParameterType(UrlParameterType::PATH)
            ->withArguments(['controller' => 'site'])
            ->withQueryParameters(['q' => 'search']);

        $this->assertSame('p', $newConfig->getPageParameterName());
        $this->assertSame(UrlParameterType::PATH, $newConfig->getPageParameterType());
        $this->assertSame(['controller' => 'site'], $newConfig->getArguments());
        $this->assertSame(['q' => 'search'], $newConfig->getQueryParameters());
    }
}
