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
use Yiisoft\Test\Support\EventDispatcher\SimpleEventDispatcher;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Formatter\Simple\SimpleMessageFormatter;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

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
                Route::get('/admin/delete')->name('admin/delete'),
                Route::get('/admin/manage')->name('admin/manage'),
                Route::get('/admin/update')->name('admin/update'),
                Route::get('/admin/view')->name('admin/resend-password'),
                Route::get('/admin/view')->name('admin/view'),
            ];
        }

        $routeCollection = self::routeCollection($routes);

        return new UrlGenerator($routeCollection, $currentRoute, $parser);
    }

    public static function webView(): WebView
    {
        return new WebView(__DIR__ . '/view', new SimpleEventDispatcher());
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
