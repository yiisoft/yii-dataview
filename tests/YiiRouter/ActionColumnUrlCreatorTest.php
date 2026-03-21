<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\YiiRouter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Router\RouteCollector;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Tests\Support\TestHelper;
use Yiisoft\Yii\DataView\Url\UrlParameterType;
use Yiisoft\Yii\DataView\YiiRouter\ActionColumnUrlCreator;
use Yiisoft\Yii\DataView\YiiRouter\ActionColumnUrlConfig;
use LogicException;

final class ActionColumnUrlCreatorTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield 'array' => [['id' => 2, 'name' => 'john']];
        yield 'object' => [
            new class {
                public int $id = 2;
            },
        ];
    }

    #[DataProvider('dataBase')]
    public function testBase($data): void
    {
        $routeMain = Route::get('/post')->name('post');
        $routeView = Route::get('/post/view')->name('post/view');
        $currentRoute = new CurrentRoute();
        $currentRoute->setRouteWithArguments($routeMain, []);
        $urlGenerator = new UrlGenerator(
            new RouteCollection(
                (new RouteCollector())->addRoute($routeMain, $routeView),
            ),
            $currentRoute,
        );

        $context = TestHelper::createDataContext(data: $data);
        $urlCreator = new ActionColumnUrlCreator(
            $urlGenerator,
            $currentRoute,
        );

        $result = ($urlCreator)('view', $context);

        $this->assertSame('/post/view?id=2', $result);
    }

    public function testUrlArgumentInPath(): void
    {
        $routeMain = Route::get('/post')->name('post');
        $routeView = Route::get('/post/view/{id}/')->name('post/view');
        $currentRoute = new CurrentRoute();
        $currentRoute->setRouteWithArguments($routeMain, []);
        $urlGenerator = new UrlGenerator(
            new RouteCollection(
                (new RouteCollector())->addRoute($routeMain, $routeView),
            ),
            $currentRoute,
        );

        $context = TestHelper::createDataContext(
            column: new ActionColumn(
                urlConfig: new ActionColumnUrlConfig(primaryKeyParameterType: UrlParameterType::Path),
            ),
            data: ['id' => 2],
        );
        $urlCreator = new ActionColumnUrlCreator(
            $urlGenerator,
            $currentRoute,
        );

        $result = ($urlCreator)('view', $context);

        $this->assertSame('/post/view/2/', $result);
    }

    public function testIncludeRequestParams(): void
    {
        $originalGet = $_GET;
        $_GET = ['sort' => '-name', 'filter' => 'active', 'page' => '3'];

        try {
            $routeMain = Route::get('/post')->name('post');
            $routeView = Route::get('/post/view')->name('post/view');
            $currentRoute = new CurrentRoute();
            $currentRoute->setRouteWithArguments($routeMain, []);
            $urlGenerator = new UrlGenerator(
                new RouteCollection(
                    (new RouteCollector())->addRoute($routeMain, $routeView),
                ),
                $currentRoute,
            );

            $context = TestHelper::createDataContext(
                column: new ActionColumn(
                    urlConfig: new ActionColumnUrlConfig(includeRequestParams: true),
                ),
                data: ['id' => 2],
            );
            $urlCreator = new ActionColumnUrlCreator(
                $urlGenerator,
                $currentRoute,
            );

            $result = ($urlCreator)('view', $context);

            $this->assertSame('/post/view?sort=-name&filter=active&page=3&id=2', $result);
        } finally {
            $_GET = $originalGet;
        }
    }

    public function testIncludeRequestParamsFiltersNonStringValues(): void
    {
        $originalGet = $_GET;
        $_GET = ['sort' => '-name', 'arr' => ['a', 'b'], 'num' => 42];

        try {
            $routeMain = Route::get('/post')->name('post');
            $routeView = Route::get('/post/view')->name('post/view');
            $currentRoute = new CurrentRoute();
            $currentRoute->setRouteWithArguments($routeMain, []);
            $urlGenerator = new UrlGenerator(
                new RouteCollection(
                    (new RouteCollector())->addRoute($routeMain, $routeView),
                ),
                $currentRoute,
            );

            $context = TestHelper::createDataContext(
                column: new ActionColumn(
                    urlConfig: new ActionColumnUrlConfig(includeRequestParams: true),
                ),
                data: ['id' => 2],
            );
            $urlCreator = new ActionColumnUrlCreator(
                $urlGenerator,
                $currentRoute,
            );

            $result = ($urlCreator)('view', $context);

            $this->assertSame('/post/view?sort=-name&id=2', $result);
        } finally {
            $_GET = $originalGet;
        }
    }

    public function testIncludeRequestParamsDisabledByDefault(): void
    {
        $originalGet = $_GET;
        $_GET = ['sort' => '-name', 'filter' => 'active'];

        try {
            $routeMain = Route::get('/post')->name('post');
            $routeView = Route::get('/post/view')->name('post/view');
            $currentRoute = new CurrentRoute();
            $currentRoute->setRouteWithArguments($routeMain, []);
            $urlGenerator = new UrlGenerator(
                new RouteCollection(
                    (new RouteCollector())->addRoute($routeMain, $routeView),
                ),
                $currentRoute,
            );

            $context = TestHelper::createDataContext(data: ['id' => 2]);
            $urlCreator = new ActionColumnUrlCreator(
                $urlGenerator,
                $currentRoute,
            );

            $result = ($urlCreator)('view', $context);

            $this->assertSame('/post/view?id=2', $result);
        } finally {
            $_GET = $originalGet;
        }
    }

    public function testIncludeRequestParamsWithExplicitQueryParameters(): void
    {
        $originalGet = $_GET;
        $_GET = ['sort' => '-name', 'page' => '3'];

        try {
            $routeMain = Route::get('/post')->name('post');
            $routeView = Route::get('/post/view')->name('post/view');
            $currentRoute = new CurrentRoute();
            $currentRoute->setRouteWithArguments($routeMain, []);
            $urlGenerator = new UrlGenerator(
                new RouteCollection(
                    (new RouteCollector())->addRoute($routeMain, $routeView),
                ),
                $currentRoute,
            );

            $context = TestHelper::createDataContext(
                column: new ActionColumn(
                    urlConfig: new ActionColumnUrlConfig(
                        queryParameters: ['extra' => 'value'],
                        includeRequestParams: true,
                    ),
                ),
                data: ['id' => 2],
            );
            $urlCreator = new ActionColumnUrlCreator(
                $urlGenerator,
                $currentRoute,
            );

            $result = ($urlCreator)('view', $context);

            $this->assertSame('/post/view?sort=-name&page=3&extra=value&id=2', $result);
        } finally {
            $_GET = $originalGet;
        }
    }

    public function testUnsupportedUrlConfig(): void
    {
        $currentRoute = new CurrentRoute();
        $urlGenerator = new UrlGenerator(
            new RouteCollection(new RouteCollector()),
            $currentRoute,
        );

        $context = TestHelper::createDataContext(
            column: new ActionColumn(urlConfig: ['primaryKey' => 'id']),
        );
        $urlCreator = new ActionColumnUrlCreator(
            $urlGenerator,
            $currentRoute,
        );

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            ActionColumnUrlCreator::class . ' supports ' . ActionColumnUrlConfig::class . ' only.',
        );
        ($urlCreator)('view', $context);
    }
}
