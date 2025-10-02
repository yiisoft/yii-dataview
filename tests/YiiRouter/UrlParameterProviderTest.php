<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\YiiRouter;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\Route;
use Yiisoft\Yii\DataView\Url\UrlParameterType;
use Yiisoft\Yii\DataView\YiiRouter\UrlParameterProvider;

final class UrlParameterProviderTest extends TestCase
{
    #[TestWith([UrlParameterType::Query])]
    #[TestWith([UrlParameterType::Path])]
    public function testGetNonExistentParameter(UrlParameterType $parameterType): void
    {
        $provider = new UrlParameterProvider(new CurrentRoute());

        $result = $provider->get('non-exists', $parameterType);

        $this->assertNull($result);
    }

    public function testGetFromPath(): void
    {
        $paramName = 'id';
        $paramValue = '42';

        $currentRoute = new CurrentRoute();
        $currentRoute->setRouteWithArguments(Route::get('/test'), [$paramName => $paramValue]);
        $provider = new UrlParameterProvider($currentRoute);

        $result = $provider->get($paramName, UrlParameterType::Path);

        $this->assertSame($paramValue, $result);
    }

    public function testGetFromQuery(): void
    {
        $paramName = 'sort';
        $paramValue = 'name';

        $_GET = [$paramName => $paramValue];
        $provider = new UrlParameterProvider(new CurrentRoute());

        $result = $provider->get($paramName, UrlParameterType::Query);

        $this->assertSame($paramValue, $result);
    }
}
