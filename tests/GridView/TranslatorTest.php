<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Definitions\Reference;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\Column\SerialColumn;
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

    protected function setUp(): void
    {
        $container = new Container(ContainerConfig::create()->withDefinitions($this->config()));
        $this->translator = $container->get(TranslatorInterface::class);

        WidgetFactory::initialize($container, []);
    }

    public function testEmptyTextTranslatorWithLocaleDefault(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
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
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator([], 10))
                ->render()
        );
    }

    public function testEmptyTextTranslatorWithLocaleSpanish(): void
    {
        $this->translator->setLocale('es');

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
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
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator([], 10))
                ->render()
        );
    }

    public function testEmptyTextTranslatorWithLocaleRussian(): void
    {
        $this->translator->setLocale('ru');

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
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
            <td colspan="4">Результатов не найдено.</td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator([], 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testSummaryTranslatorWithLocaleDefault(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
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
            <td>1</td>
            <td>1</td>
            <td>John</td>
            <td>20</td>
            </tr>
            <tr>
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            <td>21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testSummaryTranslatorWithLocaleSpanish(): void
    {
        $this->translator->setLocale('es');

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
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
            <td>1</td>
            <td>1</td>
            <td>John</td>
            <td>20</td>
            </tr>
            <tr>
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            <td>21</td>
            </tr>
            </tbody>
            </table>
            <div>Pagina <b>1</b> de <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testSummaryTranslatorWithLocaleRussian(): void
    {
        $this->translator->setLocale('ru');

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
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
            <td>1</td>
            <td>1</td>
            <td>John</td>
            <td>20</td>
            </tr>
            <tr>
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            <td>21</td>
            </tr>
            </tbody>
            </table>
            <div>Страница <b>1</b> из <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    private function config(): array
    {
        /** @psalm-var string[][] $params */
        $params = require dirname(__DIR__, 2) . '/config/params.php';
        /** @psalm-var string[] $translatorConfig */
        $containerDefinitions = require dirname(__DIR__, 2) . '/config/di.php';

        return array_merge(
            [
                TranslatorInterface::class => [
                    'class' => Translator::class,
                    '__construct()' => ['en'],
                    'addCategorySources()' => [
                        'categories' => Reference::to('tag@translation.categorySource'),
                    ],
                ],
                ValidatorInterface::class => Validator::class,
            ],
            $containerDefinitions,
        );
    }
}
