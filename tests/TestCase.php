<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Di\Container;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\CurrentRouteInterface;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\FastRoute\UrlMatcher;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Router\RouteCollectionInterface;
use Yiisoft\Router\RouteCollector;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Formatter\Intl\IntlMessageFormatter;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\Columns\ActionColumn;
use Yiisoft\Yii\DataView\Columns\CheckboxColumn;
use Yiisoft\Yii\DataView\Columns\DataColumn;
use Yiisoft\Yii\DataView\Columns\RadioButtonColumn;
use Yiisoft\Yii\DataView\Factory\GridViewFactory;
use Yiisoft\Yii\DataView\GridView;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected ActionColumn $actionColumn;
    protected CheckBoxColumn $checkBoxColumn;
    protected CurrentRouteInterface $currentRoute;
    protected DataColumn $dataColumn;
    protected GridViewFactory $gridViewFactory;
    protected RadioButtonColumn $radioButtonColumn;
    protected WebView $webView;
    private ContainerInterface $container;
    private PaginatorInterface $paginator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configContainer();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset(
            $this->actionColumn,
            $this->checkBoxColumn,
            $this->dataColumn,
            $this->container,
            $this->gridViewFactory,
            $this->paginator,
            $this->radioButtonColumn,
            $this->urlMatcher,
            $this->webView,
        );
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

    protected function createGridView(
        array $columns = [],
        int $currentPage = 1,
        int $pageSize = 5,
        string $cssFramework = GridView::BOOTSTRAP
    ): GridView {
        return GridView::widget()
            ->columns($columns)
            ->currentPage($currentPage)
            ->cssFramework($cssFramework)
            ->pageSize($pageSize);
    }

    protected function createOffsetPaginator(array $sortParams = []): OffsetPaginator
    {
        $dataReader = new IterableDataReader([
            ['id' => '1', 'name' => 'tests 1', 'total' => '10'],
            ['id' => '2', 'name' => 'tests 2', 'total' => '20'],
            ['id' => '3', 'name' => 'tests 3', 'total' => '30'],
            ['id' => '4', 'name' => 'tests 4', 'total' => '40'],
            ['id' => '5', 'name' => 'tests 5', 'total' => '50'],
            ['id' => '6', 'name' => 'tests 6', 'total' => '60'],
        ]);

        if ($sortParams !== []) {
            $sort = Sort::only($sortParams);
            $dataReader = $dataReader->withSort($sort);
        }

        return new OffsetPaginator($dataReader);
    }

    private function configContainer(): void
    {
        $this->container = new Container($this->config());

        WidgetFactory::initialize($this->container, []);

        $this->actionColumn = $this->container->get(ActionColumn::class);
        $this->checkboxColumn = $this->container->get(CheckboxColumn::class);
        $this->currentRoute = $this->container->get(CurrentRouteInterface::class);
        $this->dataColumn = $this->container->get(DataColumn::class);
        $this->gridViewFactory = $this->container->get(GridViewFactory::class);
        $this->radioButtonColumn = $this->container->get(RadioButtonColumn::class);
        $this->webView = $this->container->get(WebView::class);
    }

    private function config(): array
    {
        return [
            Aliases::class => [
                'class' => Aliases::class,
                '__construct()' => [['@grid-view-translation' => dirname(__DIR__) . '/src/Translation']],
            ],

            LoggerInterface::class => NullLogger::class,

            ListenerProviderInterface::class => Provider::class,

            EventDispatcherInterface::class => Dispatcher::class,

            CurrentRouteInterface::class => CurrentRoute::class,

            WebView::class => [
                'class' => WebView::class,
                '__construct()' => [
                    'basePath' => __DIR__ . '/runtime',
                ],
            ],

            UrlGeneratorInterface::class => UrlGenerator::class,

            UrlMatcherInterface::class => UrlMatcher::class,

            RouteCollectionInterface::class => static function (RouteCollector $collector) {
                $collector
                    ->addGroup(
                        Group::create(null)
                            ->routes(
                                Route::methods(['GET', 'POST'], '/admin/index')->action([TestDelete::class, 'run'])
                                    ->name('admin'),
                                Route::methods(['GET', 'POST'], '/admin/delete[/{id}]')->action([TestDelete::class, 'run'])
                                    ->name('delete'),
                                Route::methods(['GET', 'POST'], '/admin/update[/{id}]')->action([TestUpdate::class, 'run'])
                                    ->name('update'),
                                Route::methods(['GET', 'POST'], '/admin/view[/{id}]')->action([TestView::class, 'run'])
                                    ->name('view'),
                                Route::methods(['GET', 'POST'], '/admin/custom[/{id}]')->action([TestCustom::class, 'run'])
                                    ->name('admin/custom'),
                            ),
                    );

                return new RouteCollection($collector);
            },

            MessageReaderInterface::class => [
                'class' => MessageSource::class,
                '__construct()' => [
                    DynamicReference::to(
                        static fn (Aliases $aliases) => $aliases->get('@grid-view-translation'),
                    ),
                ],
            ],

            MessageFormatterInterface::class => IntlMessageFormatter::class,

            CategorySource::class => [
                'class' => CategorySource::class,
                '__construct()' => [
                    'name' => 'yii-gridview',
                ],
            ],

            TranslatorInterface::class => static function (
                MessageReaderInterface $messageReader,
                MessageFormatterInterface $messageFormatter
            ) {
                $translator = new Translator('en');
                $categorySource = new CategorySource('yii-gridview', $messageReader, $messageFormatter);
                $translator->addCategorySource($categorySource);

                return $translator;
            },
        ];
    }
}
