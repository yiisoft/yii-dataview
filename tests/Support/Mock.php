<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use FastRoute\RouteParser;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Router\RouteCollectionInterface;
use Yiisoft\Router\RouteCollector;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class Mock extends TestCase
{
    public static function category(string $category, string $path): CategorySource
    {
        $messageSource = new MessageSource($path);

        return new CategorySource($category, $messageSource, new SimpleMessageFormatter());
    }

    public static function translator(
        string $locale,
        string $fallbackLocale = null,
        EventDispatcherInterface $eventDispatcher = null
    ): TranslatorInterface {
        return new Translator($locale, $fallbackLocale, $eventDispatcher);
    }

    /**
     * @psalm-param array<Route> $routes
     */
    public static function urlGenerator(
        array $routes = [],
        CurrentRoute $currentRoute = null,
        RouteParser $parser = null
    ): UrlGeneratorInterface {
        if ($routes === []) {
            $routes = [
                Route::get('/admin/manage')->name('admin/manage'),
                Route::get('/admin/manage/delete')->name('admin/manage/delete'),
                Route::get('/admin/manage/resend-password')->name('admin/manage/resend-password'),
                Route::get('/admin/manage/update')->name('admin/manage/update'),
                Route::get('/admin/manage/view')->name('admin/manage/view'),
            ];
        }

        $routeCollection = self::routeCollection($routes);

        return new UrlGenerator($routeCollection, $currentRoute, $parser);
    }

    /**
     * @psalm-param array<Route> $routes
     */
    private static function routeCollection(array $routes): RouteCollectionInterface
    {
        $rootGroup = Group::create()->routes(...$routes);
        $collector = new RouteCollector();
        $collector->addGroup($rootGroup);

        return new RouteCollection($collector);
    }
}
