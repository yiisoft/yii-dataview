<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\YiiRouter;

use PHPUnit\Framework\TestCase;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Router\RouteCollector;
use Yiisoft\Yii\DataView\YiiRouter\UrlCreator;

final class UrlCreatorTest extends TestCase
{
    public function testBase(): void
    {
        $route = Route::get('/users/{name}/{page}');
        $currentRoute = new CurrentRoute();
        $currentRoute->setRouteWithArguments($route, ['name' => 'john', 'page' => '1']);
        $urlGenerator = new UrlGenerator(
            new RouteCollection(
                (new RouteCollector())->addRoute($route),
            ),
            $currentRoute,
        );

        $result = (new UrlCreator($urlGenerator))(
            ['page' => 2],
            ['sort' => 'name', 'dir' => 'asc'],
        );

        $this->assertSame('/users/john/2?sort=name&dir=asc', $result);
    }
}
