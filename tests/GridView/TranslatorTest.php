<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Di\NotFoundException;
use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\Column\GridView\DataColumn;
use Yiisoft\Yii\DataView\Column\GridView\SerialColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

use function array_merge;

final class TranslatorTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John', 'age' => 20],
        ['id' => 2, 'name' => 'Mary', 'age' => 21],
    ];
    private TranslatorInterface $translator;

    /**
     * @throws CircularReferenceException
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws NotFoundException
     */
    protected function setUp(): void
    {
        $container = new Container(ContainerConfig::create()->withDefinitions($this->config()));
        $this->translator = $container->get(TranslatorInterface::class);

        WidgetFactory::initialize($container, []);
    }

    /**
     * @throws InvalidConfigException
     * @throws \Yiisoft\Factory\NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testEmptyTextTranslatorWithLocaleDefault(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td colspan="4">No results found.</td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator([], 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws \Yiisoft\Factory\NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testEmptyTextTranslatorWithLocaleSpanish(): void
    {
        $this->translator->setLocale('es');

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td colspan="4">No se han encontrado resultados.</td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator([], 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws \Yiisoft\Factory\NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testEmptyTextTranslatorWithLocaleRussian(): void
    {
        $this->translator->setLocale('ru');

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td colspan="4">результатов не найдено</td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator([], 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws \Yiisoft\Factory\NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testSummaryTranslatorWithLocaleDefault(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="age">20</td>
            </tr>
            <tr>
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws \Yiisoft\Factory\NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testSummaryTranslatorWithLocaleSpanish(): void
    {
        $this->translator->setLocale('es');

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="age">20</td>
            </tr>
            <tr>
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            </tr>
            </tbody>
            </table>
            <div>Pagina <b>1</b> de <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws \Yiisoft\Factory\NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testSummaryTranslatorWithLocaleRussian(): void
    {
        $this->translator->setLocale('ru');

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="age">20</td>
            </tr>
            <tr>
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            </tr>
            </tbody>
            </table>
            <div>Страница <b>1</b> из <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    private function config(): array
    {
        /** @psalm-var string[][] $params */
        $params = require dirname(__DIR__, 2) . '/config/params.php';
        /** @psalm-var string[] $translatorConfig */
        $translatorConfig = require dirname(__DIR__, 2) . '/config/common/translator-yii-dataview.php';

        return array_merge(
            [
                Aliases::class => [
                    'class' => Aliases::class,
                    '__construct()' => [$params['yiisoft/aliases']['aliases']],
                ],

                MessageFormatterInterface::class => SimpleMessageFormatter::class,

                TranslatorInterface::class => [
                    'class' => Translator::class,
                    '__construct()' => ['en'],
                    'addCategorySources()' => [
                        $params['yiisoft/translator']['categorySources'],
                    ],
                ],
            ],
            $translatorConfig,
        );
    }

    /**
     * @psalm-return array<SerialColumn|DataColumn>
     */
    private function createColumns(): array
    {
        return [
            SerialColumn::create(),
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            DataColumn::create()->attribute('age'),
        ];
    }
}
