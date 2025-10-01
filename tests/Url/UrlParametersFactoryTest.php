<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Url;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Yii\DataView\Url\UrlConfig;
use Yiisoft\Yii\DataView\Url\UrlParametersFactory;
use Yiisoft\Yii\DataView\Url\UrlParameterType;

final class UrlParametersFactoryTest extends TestCase
{
    public function testNextPageTokenInQueryParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::Query,
            previousPageParameterType: UrlParameterType::Query,
            pageSizeParameterType: UrlParameterType::Query,
            sortParameterType: UrlParameterType::Query,
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            PageToken::next('token123'),
            null,
            null,
            $config,
        );

        $this->assertSame([], $arguments);
        $this->assertSame(
            [
                'page' => 'token123',
                'prev-page' => null,
                'pagesize' => null,
                'sort' => null,
            ],
            $queryParameters,
        );
    }

    public function testNextPageTokenInPathParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::Path,
            previousPageParameterType: UrlParameterType::Query,
            pageSizeParameterType: UrlParameterType::Query,
            sortParameterType: UrlParameterType::Query
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            PageToken::next('token123'),
            null,
            null,
            $config
        );

        $this->assertSame(['page' => 'token123'], $arguments);
        $this->assertSame(
            [
                'prev-page' => null,
                'pagesize' => null,
                'sort' => null,
            ],
            $queryParameters,
        );
    }

    public function testPreviousPageTokenInQueryParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::Query,
            previousPageParameterType: UrlParameterType::Query,
            pageSizeParameterType: UrlParameterType::Query,
            sortParameterType: UrlParameterType::Query
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            PageToken::previous('token123'),
            null,
            null,
            $config,
        );

        $this->assertSame([], $arguments);
        $this->assertSame(
            [
                'page' => null,
                'prev-page' => 'token123',
                'pagesize' => null,
                'sort' => null,
            ],
            $queryParameters,
        );
    }

    public function testPreviousPageTokenInPathParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::Query,
            previousPageParameterType: UrlParameterType::Path,
            pageSizeParameterType: UrlParameterType::Query,
            sortParameterType: UrlParameterType::Query
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            PageToken::previous('token123'),
            null,
            null,
            $config,
        );

        $this->assertSame(['prev-page' => 'token123'], $arguments);
        $this->assertSame(
            [
                'page' => null,
                'pagesize' => null,
                'sort' => null,
            ],
            $queryParameters,
        );
    }

    public function testPageSizeInQueryParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::Query,
            previousPageParameterType: UrlParameterType::Query,
            pageSizeParameterType: UrlParameterType::Query,
            sortParameterType: UrlParameterType::Query
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            null,
            20,
            null,
            $config,
        );

        $this->assertSame([], $arguments);
        $this->assertSame(
            [
                'page' => null,
                'prev-page' => null,
                'pagesize' => 20,
                'sort' => null,
            ],
            $queryParameters,
        );
    }

    public function testPageSizeInPathParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::Query,
            previousPageParameterType: UrlParameterType::Query,
            pageSizeParameterType: UrlParameterType::Path,
            sortParameterType: UrlParameterType::Query
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            null,
            20,
            null,
            $config,
        );

        $this->assertSame(['pagesize' => 20], $arguments);
        $this->assertSame(
            [
                'page' => null,
                'prev-page' => null,
                'sort' => null,
            ],
            $queryParameters,
        );
    }

    public function testSortInQueryParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::Query,
            previousPageParameterType: UrlParameterType::Query,
            pageSizeParameterType: UrlParameterType::Query,
            sortParameterType: UrlParameterType::Query
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            null,
            null,
            'name,-date',
            $config,
        );

        $this->assertSame([], $arguments);
        $this->assertSame(
            [
                'page' => null,
                'prev-page' => null,
                'pagesize' => null,
                'sort' => 'name,-date',
            ],
            $queryParameters,
        );
    }

    public function testSortInPathParameter(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::Query,
            previousPageParameterType: UrlParameterType::Query,
            pageSizeParameterType: UrlParameterType::Query,
            sortParameterType: UrlParameterType::Path
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            null,
            null,
            'name,-date',
            $config,
        );

        $this->assertSame(['sort' => 'name,-date'], $arguments);
        $this->assertSame(
            [
                'page' => null,
                'prev-page' => null,
                'pagesize' => null,
            ],
            $queryParameters,
        );
    }

    public function testCustomParameterNames(): void
    {
        $config = new UrlConfig(
            pageParameterName: 'p',
            previousPageParameterName: 'pp',
            pageSizeParameterName: 'ps',
            sortParameterName: 's',
            pageParameterType: UrlParameterType::Query,
            previousPageParameterType: UrlParameterType::Path,
            pageSizeParameterType: UrlParameterType::Query,
            sortParameterType: UrlParameterType::Query
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            PageToken::previous('token123'),
            null,
            null,
            $config,
        );

        $this->assertSame(['pp' => 'token123'], $arguments);
        $this->assertSame(
            [
                'p' => null,
                'ps' => null,
                's' => null,
            ],
            $queryParameters,
        );
    }

    public function testExistingArgumentsAndQueryParameters(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::Query,
            previousPageParameterType: UrlParameterType::Query,
            pageSizeParameterType: UrlParameterType::Query,
            sortParameterType: UrlParameterType::Query,
            arguments: ['id' => 123],
            queryParameters: ['filter' => 'active']
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            null,
            null,
            null,
            $config,
        );

        $this->assertSame(['id' => 123], $arguments);
        $this->assertSame(
            [
                'filter' => 'active',
                'page' => null,
                'prev-page' => null,
                'pagesize' => null,
                'sort' => null,
            ],
            $queryParameters,
        );
    }

    public function testMixedParameterTypesAndValues(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::Path,
            previousPageParameterType: UrlParameterType::Query,
            pageSizeParameterType: UrlParameterType::Path,
            sortParameterType: UrlParameterType::Query,
            arguments: ['id' => 123],
            queryParameters: ['filter' => 'active']
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            PageToken::next('token123'),
            20,
            'name,-date',
            $config,
        );

        $this->assertSame(
            [
                'id' => 123,
                'page' => 'token123',
                'pagesize' => 20,
            ],
            $arguments,
        );
        $this->assertSame(
            [
                'filter' => 'active',
                'prev-page' => null,
                'sort' => 'name,-date',
            ],
            $queryParameters,
        );
    }

    public function testNullPageToken(): void
    {
        $config = new UrlConfig(
            pageParameterType: UrlParameterType::Path,
            previousPageParameterType: UrlParameterType::Path,
            pageSizeParameterType: UrlParameterType::Query,
            sortParameterType: UrlParameterType::Query
        );

        [$arguments, $queryParameters] = UrlParametersFactory::create(
            null,
            20,
            'name,-date',
            $config,
        );

        $this->assertSame(
            [
                'page' => null,
                'prev-page' => null,
            ],
            $arguments,
        );
        $this->assertSame(
            [
                'pagesize' => 20,
                'sort' => 'name,-date',
            ],
            $queryParameters,
        );
    }
}
