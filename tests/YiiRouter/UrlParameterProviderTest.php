<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\YiiRouter;

use PHPUnit\Framework\TestCase;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\Route;
use Yiisoft\Yii\DataView\UrlParameterType;
use Yiisoft\Yii\DataView\YiiRouter\UrlParameterProvider;

final class UrlParameterProviderTest extends TestCase
{
    public function testGetNonExistentParameter(): void
    {
        $paramName = 'nonexistent';

        $currentRoute = new CurrentRoute();
        $currentRoute->setRouteWithArguments(Route::get('/test')->name('test'), [$paramName => null]);

        /** @var CurrentRoute $currentRoute */
        $provider = new UrlParameterProvider($currentRoute);

        $this->assertNull($provider->get($paramName, UrlParameterType::PATH));
        $this->assertNull($provider->get($paramName, UrlParameterType::QUERY));
    }

    public function testGetRouteWithArgument(): void
    {
        $paramName = 'id';
        $paramValue = '42';

        $currentRoute = new CurrentRoute();
        $currentRoute->setRouteWithArguments(Route::get('/test')->name('test'), [$paramName => $paramValue]);

        /** @var CurrentRoute $currentRoute */
        $provider = new UrlParameterProvider($currentRoute);

        $this->assertSame($paramValue, $provider->get($paramName, UrlParameterType::PATH));
    }

    public function testGetRouteWithQueryParameter(): void
    {
        $_GET = [];

        $paramName = 'sort';
        $paramValue = 'name';

        $_GET[$paramName] = $paramValue;

        $currentRoute = new CurrentRoute();
        $currentRoute->setRouteWithArguments(Route::get('/test')->name('test'), []);

        /** @var CurrentRoute $currentRoute */
        $provider = new UrlParameterProvider($currentRoute);

        $this->assertSame($paramValue, $provider->get($paramName, UrlParameterType::QUERY));

        $_GET = [];
    }

    public function testGetWithInvalidType(): void
    {
        $paramName = 'id';

        $currentRoute = new CurrentRoute();
        $currentRoute->setRouteWithArguments(Route::get('/test')->name('test'), [$paramName => '42']);

        /** @var CurrentRoute $currentRoute */
        $provider = new UrlParameterProvider($currentRoute);

        $this->assertNull($provider->get($paramName, 0));
    }
}
