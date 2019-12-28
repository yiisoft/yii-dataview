<?php

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Factory\Definitions\Reference;
use Yiisoft\I18n\MessageFormatterInterface;
use Yiisoft\View\Theme;
use Yiisoft\View\View;
use Yiisoft\View\ViewContextInterface;
use Yiisoft\Yii\DataView\BaseListView;
use Yiisoft\Yii\DataView\Columns\Column;
use Yiisoft\Yii\DataView\DetailView;

return [
    MessageFormatterInterface::class => static function () {
        return new class implements MessageFormatterInterface {
            /**
             * @inheritDoc
             */
            public function format(string $message, array $parameters, string $language): string
            {
                return $message;
            }
        };
    },
    ViewContextInterface::class => View::class,
    BaseListView::class => [
        '__class' => BaseListView::class,
        '__construct()' => [
            Reference::to(MessageFormatterInterface::class),
            Reference::to(ViewContextInterface::class),
            Reference::to(Aliases::class),
        ],
    ],
    DetailView::class => [
        '__class' => DetailView::class,
        '__construct()' => [
            Reference::to(MessageFormatterInterface::class),
        ],
    ],
    Column::class => [
        '__class' => Column::class,
        '__construct()' => [
            Reference::to(MessageFormatterInterface::class),
        ],
    ],
    Aliases::class => [
        '@root' => dirname(__DIR__, 1),
        '@public' => '@root/tests/public',
        '@basePath' => '@public',
        '@baseUrl' => '/baseUrl',
        '@converter' => '@public/assetconverter',
        '@npm' => '@root/node_modules',
        '@view' => '@public/view',
        '@web' => '@baseUrl',
        '@testSourcePath' => '@public/assetsources',
    ],
    View::class => static function (ContainerInterface $container) {
        $aliases = $container->get(Aliases::class);
        $eventDispatcher = $container->get(EventDispatcherInterface::class);
        $theme = $container->get(Theme::class);
        $logger = $container->get(LoggerInterface::class);

        return new View($aliases->get('@view'), $theme, $eventDispatcher, $logger);
    },
];
