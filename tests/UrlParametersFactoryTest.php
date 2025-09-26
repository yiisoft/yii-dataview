<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Yii\DataView\Url\UrlConfig;
use Yiisoft\Yii\DataView\Url\UrlParametersFactory;
use Yiisoft\Yii\DataView\Url\UrlParameterType;

/**
 * @covers \Yiisoft\Yii\DataView\Url\UrlParametersFactory
 * @covers \Yiisoft\Yii\DataView\Url\UrlConfig
 */
final class UrlParametersFactoryTest extends TestCase
{
    public function testCreateWithNextPageTokenInQueryParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::QUERY,
            previousPageParameterType: UrlParameterType::QUERY,
            pageSizeParameterType: UrlParameterType::QUERY,
            sortParameterType: UrlParameterType::QUERY
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            PageToken::next('token123'),
            null,
            null,
            $config
        );

        $this->assertSame([], $arguments);
        $this->assertSame([
            'page' => 'token123',
            'prev-page' => null,
            'pagesize' => null,
            'sort' => null,
        ], $queryParameters);
    }

    public function testCreateWithNextPageTokenInPathParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::PATH,
            previousPageParameterType: UrlParameterType::QUERY,
            pageSizeParameterType: UrlParameterType::QUERY,
            sortParameterType: UrlParameterType::QUERY
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            PageToken::next('token123'),
            null,
            null,
            $config
        );

        $this->assertSame(['page' => 'token123'], $arguments);
        $this->assertSame([
            'prev-page' => null,
            'pagesize' => null,
            'sort' => null,
        ], $queryParameters);
    }

    public function testCreateWithPreviousPageTokenInQueryParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::QUERY,
            previousPageParameterType: UrlParameterType::QUERY,
            pageSizeParameterType: UrlParameterType::QUERY,
            sortParameterType: UrlParameterType::QUERY
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            PageToken::previous('token123'),
            null,
            null,
            $config
        );

        $this->assertSame([], $arguments);
        $this->assertSame([
            'page' => null,
            'prev-page' => 'token123',
            'pagesize' => null,
            'sort' => null,
        ], $queryParameters);
    }

    public function testCreateWithPreviousPageTokenInPathParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::QUERY,
            previousPageParameterType: UrlParameterType::PATH,
            pageSizeParameterType: UrlParameterType::QUERY,
            sortParameterType: UrlParameterType::QUERY
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            PageToken::previous('token123'),
            null,
            null,
            $config
        );

        $this->assertSame(['prev-page' => 'token123'], $arguments);
        $this->assertSame([
            'page' => null,
            'pagesize' => null,
            'sort' => null,
        ], $queryParameters);
    }

    public function testCreateWithPageSizeInQueryParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::QUERY,
            previousPageParameterType: UrlParameterType::QUERY,
            pageSizeParameterType: UrlParameterType::QUERY,
            sortParameterType: UrlParameterType::QUERY
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            null,
            20,
            null,
            $config
        );

        $this->assertSame([], $arguments);
        $this->assertSame([
            'page' => null,
            'prev-page' => null,
            'pagesize' => 20,
            'sort' => null,
        ], $queryParameters);
    }

    public function testCreateWithPageSizeInPathParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::QUERY,
            previousPageParameterType: UrlParameterType::QUERY,
            pageSizeParameterType: UrlParameterType::PATH,
            sortParameterType: UrlParameterType::QUERY
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            null,
            20,
            null,
            $config
        );

        $this->assertSame(['pagesize' => 20], $arguments);
        $this->assertSame([
            'page' => null,
            'prev-page' => null,
            'sort' => null,
        ], $queryParameters);
    }

    public function testCreateWithSortInQueryParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::QUERY,
            previousPageParameterType: UrlParameterType::QUERY,
            pageSizeParameterType: UrlParameterType::QUERY,
            sortParameterType: UrlParameterType::QUERY
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            null,
            null,
            'name,-date',
            $config
        );

        $this->assertSame([], $arguments);
        $this->assertSame([
            'page' => null,
            'prev-page' => null,
            'pagesize' => null,
            'sort' => 'name,-date',
        ], $queryParameters);
    }

    public function testCreateWithSortInPathParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::QUERY,
            previousPageParameterType: UrlParameterType::QUERY,
            pageSizeParameterType: UrlParameterType::QUERY,
            sortParameterType: UrlParameterType::PATH
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            null,
            null,
            'name,-date',
            $config
        );

        $this->assertSame(['sort' => 'name,-date'], $arguments);
        $this->assertSame([
            'page' => null,
            'prev-page' => null,
            'pagesize' => null,
        ], $queryParameters);
    }

    public function testCreateWithCustomParameterNames(): void
    {
        $config = new UrlConfig(
            pageParameterName: 'p',
            previousPageParameterName: 'pp',
            pageSizeParameterName: 'ps',
            sortParameterName: 's',
            pageParameterType: UrlParameterType::QUERY,
            previousPageParameterType: UrlParameterType::PATH,
            pageSizeParameterType: UrlParameterType::QUERY,
            sortParameterType: UrlParameterType::QUERY
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            PageToken::previous('token123'),
            null,
            null,
            $config
        );

        $this->assertSame(['pp' => 'token123'], $arguments);
        $this->assertSame([
            'p' => null,
            'ps' => null,
            's' => null,
        ], $queryParameters);
    }

    public function testCreateWithExistingArgumentsAndQueryParameters(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::QUERY,
            previousPageParameterType: UrlParameterType::QUERY,
            pageSizeParameterType: UrlParameterType::QUERY,
            sortParameterType: UrlParameterType::QUERY,
            arguments: ['id' => 123],
            queryParameters: ['filter' => 'active']
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            null,
            null,
            null,
            $config
        );

        $this->assertSame(['id' => 123], $arguments);
        $this->assertSame([
            'filter' => 'active',
            'page' => null,
            'prev-page' => null,
            'pagesize' => null,
            'sort' => null,
        ], $queryParameters);
    }

    public function testCreateWithMixedParameterTypesAndValues(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::PATH,
            previousPageParameterType: UrlParameterType::QUERY,
            pageSizeParameterType: UrlParameterType::PATH,
            sortParameterType: UrlParameterType::QUERY,
            arguments: ['id' => 123],
            queryParameters: ['filter' => 'active']
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            PageToken::next('token123'),
            20,
            'name,-date',
            $config
        );

        $this->assertSame([
            'id' => 123,
            'page' => 'token123',
            'pagesize' => 20,
        ], $arguments);
        $this->assertSame([
            'filter' => 'active',
            'prev-page' => null,
            'sort' => 'name,-date',
        ], $queryParameters);
    }

    public function testCreateWithNullPageToken(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::PATH,
            previousPageParameterType: UrlParameterType::PATH,
            pageSizeParameterType: UrlParameterType::QUERY,
            sortParameterType: UrlParameterType::QUERY
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            null,
            20,
            'name,-date',
            $config
        );

        $this->assertSame([
            'page' => null,
            'prev-page' => null,
        ], $arguments);
        $this->assertSame([
            'pagesize' => 20,
            'sort' => 'name,-date',
        ], $queryParameters);
    }
}
