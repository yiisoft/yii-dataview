<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests;

use Psr\Container\ContainerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Di\Container;
use Yiisoft\Factory\Definitions\Reference;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\FastRoute\UrlMatcher;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Router\RouteCollectionInterface;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Formatter\Intl\IntlMessageFormatter;
use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\Columns\ActionColumn;
use Yiisoft\Yii\DataView\Columns\CheckboxColumn;
use Yiisoft\Yii\DataView\GridView;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected ActionColumn $actionColumn;
    protected CheckBoxColumn $checkBoxColumn;
    protected GridView $gridView;
    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configContainer();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->actionColumn, $this->checkBoxColumn, $this->container, $this->gridView);
    }

    /**
     * Asserting two strings equality ignoring line endings.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     */
    protected function assertEqualsWithoutLE(string $expected, string $actual, string $message = ''): void
    {
        $expected = str_replace("\r\n", "\n", $expected);
        $actual = str_replace("\r\n", "\n", $actual);

        $this->assertEquals($expected, $actual, $message);
    }

    private function configContainer(): void
    {
        $this->container = new Container($this->config());

        WidgetFactory::initialize($this->container, []);

        $this->actionColumn = $this->container->get(ActionColumn::class);
        $this->checkboxColumn = $this->container->get(CheckboxColumn::class);
        $this->gridView = $this->container->get(GridView::class);

    }

    private function config(): array
    {
        return [
            Aliases::class => [
                '__class' => Aliases::class,
                '__construct()' => [['@grid-view-translation' => dirname(__DIR__) . '/src/Translation']],
            ],

            UrlGeneratorInterface::class => UrlGenerator::class,

            UrlMatcherInterface::class => UrlMatcher::class,

            RouteCollectorInterface::class => Group::create(
                null,
                [
                    Route::methods(['GET', 'POST'], '/admin/delete[/{id}]', [TestDelete::class, 'run'])
                        ->name('delete'),
                    Route::methods(['GET', 'POST'], '/admin/update[/{id}]', [TestUpdate::class, 'run'])
                        ->name('update'),
                    Route::methods(['GET', 'POST'], '/admin/view[/{id}]', [TestView::class, 'run'])
                        ->name('view'),
                    Route::methods(['GET', 'POST'], '/admin/custom[/{id}]', [TestCustom::class, 'run'])
                        ->name('admin/custom'),
                ]
            ),

            RouteCollectionInterface::class => RouteCollection::class,

            MessageReaderInterface::class => [
                '__class' => MessageSource::class,
                '__construct()' => [fn (Aliases $aliases) => $aliases->get('@grid-view-translation')],
            ],

            MessageFormatterInterface::class => IntlMessageFormatter::class,

            CategorySource::class => [
                '__class' => CategorySource::class,
                '__construct()' => [
                    'name' => 'yii-gridview',
                ],
            ],

            TranslatorInterface::class => [
                '__class' => Translator:: class,
                '__construct()' => [
                    'locale' => 'en',
                ],
                'addCategorySource()' => [Reference::to(CategorySource::class)],
            ],

            ReadableDataInterface::class => static fn () => new IterableDataReader(),

            PaginatorInterface::class => OffsetPaginator::class,
        ];
    }
}
