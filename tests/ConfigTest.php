<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\BaseListView;
use Yiisoft\Yii\DataView\DetailView\DetailView;
use Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter;
use Yiisoft\Yii\DataView\Filter\Widget\TextInputFilter;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\ListView\ListView;
use Yiisoft\Yii\DataView\PageSize\InputPageSize;
use Yiisoft\Yii\DataView\PageSize\SelectPageSize;
use Yiisoft\Yii\DataView\Pagination\KeysetPagination;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;

use function dirname;

final class ConfigTest extends TestCase
{
    public function testTranslator(): void
    {
        $translator = $this->createContainer()->get(TranslatorInterface::class);

        $result = $translator->withLocale('ru')->translate(
            'Actions',
            category: BaseListView::DEFAULT_TRANSLATION_CATEGORY,
        );

        $this->assertSame('Действия', $result);
    }

    public function testWidgetsThemes(): void
    {
        $config = require dirname(__DIR__) . '/config/widgets-themes.php';

        WidgetFactory::initialize(
            container: $this->createContainer(),
            themes: $config,
        );
        GridView::widget();
        DetailView::widget();
        ListView::widget();
        DropdownFilter::widget();
        TextInputFilter::widget();
        OffsetPagination::widget();
        KeysetPagination::widget();
        InputPageSize::widget();
        SelectPageSize::widget();

        $this->expectNotToPerformAssertions();
    }

    private function createContainer(): Container
    {
        return new Container(
            ContainerConfig::create()->withDefinitions(
                $this->getContainerDefinitions()
            )
        );
    }

    private function getContainerDefinitions(): array
    {
        $params = array_merge(
            require dirname(__DIR__) . '/config/params.php',
            require dirname(__DIR__) . '/vendor/yiisoft/translator/config/params.php',
        );
        return array_merge(
            require dirname(__DIR__) . '/config/di.php',
            require dirname(__DIR__) . '/vendor/yiisoft/translator/config/di.php',
        );
    }
}
