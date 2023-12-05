<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Html\Html;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\Route;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\ActionColumnRenderer;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\StringableObject;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\YiiRouter\UrlConfig;

final class ActionColumnTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John', 'age' => 20],
        ['id' => 2, 'name' => 'Mary', 'age' => 21],
    ];

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testContent(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a class="text-decoration-none" href="/admin/view?id=1" title="View">🔎</a>
            </td>
            </tr>
            <tr>
            <td>
            <a class="text-decoration-none" href="/admin/view?id=2" title="View">🔎</a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new ActionColumn(
                        content: static fn(array $data): string => Html::a()
                            ->addAttributes(['class' => 'text-decoration-none', 'title' => 'View'])
                            ->content('🔎')
                            ->encode(false)
                            ->href('/admin/view?id=' . $data['id'])
                            ->render(),
                    )
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
    public function testContentAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td class="text-decoration-none test.class">
            <a href="/admin/view?id=1" title="View">🔎</a>
            </td>
            </tr>
            <tr>
            <td class="text-decoration-none test.class">
            <a href="/admin/view?id=2" title="View">🔎</a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new ActionColumn(
                        content: static fn(array $data): string => Html::a()
                            ->addAttributes(['title' => 'View'])
                            ->content('🔎')
                            ->encode(false)
                            ->href('/admin/view?id=' . $data['id'])
                            ->render(),
                        bodyAttributes: ['class' => 'text-decoration-none test.class']
                    ),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testCustomButton(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a class="text-decoration-none" href="/admin/manage/resend-password?id=1" title="Resend password">&#128274;</a>
            </td>
            </tr>
            <tr>
            <td>
            <a class="text-decoration-none" href="/admin/manage/resend-password?id=2" title="Resend password">&#128274;</a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new ActionColumn(
                        buttons: [
                            'resend-password' => static fn(string $url): string => Html::a()
                                ->addAttributes(['class' => 'text-decoration-none', 'title' => 'Resend password'])
                                ->content('&#128274;')
                                ->encode(false)
                                ->href($url)
                                ->render(),
                        ],
                        template: '{resend-password}',
                        visibleButtons: ['resend-password' => true]
                    ),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testFooterAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
            <td class="test.class">test.footer</td>
            </tr>
            </tfoot>
            <tbody>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new ActionColumn(
                        footer: 'test.footer',
                        footerAttributes: ['class' => 'test.class'],
                    ),
                )
                ->footerEnabled(true)
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testLabel(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>test.label</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(new ActionColumn(header: 'test.label'))
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
    public function testLabelWithMbString(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Ενέργειες</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(new ActionColumn(header: 'Ενέργειες'))
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
    public function testLabelAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th class="test.class">test.label</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new ActionColumn(
                        header: 'test.label',
                        headerAttributes: ['class' => 'test.class'],
                    ),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testNotVisible(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr></tr>
            </thead>
            <tbody>
            <tr></tr>
            <tr></tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(new ActionColumn(visible: false))
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testPrimaryKey(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?identity_id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?identity_id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?identity_id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?identity_id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?identity_id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?identity_id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(new ActionColumn(urlConfig: new UrlConfig('identity_id')))
                ->id('w1-grid')
                ->dataReader(
                    $this->createOffsetPaginator(
                        [
                            ['identity_id' => 1, 'name' => 'John', 'age' => 20],
                            ['identity_id' => 2, 'name' => 'Mary', 'age' => 21],
                        ],
                        10
                    )
                )
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testRender(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            <td>
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td>2</td>
            <td>Mary</td>
            <td>
            <a name="view" href="/admin/manage/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new ActionColumn(),
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
    public function testUrlArguments(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?test-arguments=test.arguments&amp;id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?test-arguments=test.arguments&amp;id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?test-arguments=test.arguments&amp;id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?test-arguments=test.arguments&amp;id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?test-arguments=test.arguments&amp;id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?test-arguments=test.arguments&amp;id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(new ActionColumn(urlConfig: new UrlConfig(arguments: ['test-arguments' => 'test.arguments'])))
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
    public function testUrlCreator(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a name="view" href="https://test.com/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="https://test.com/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="https://test.com/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td>
            <a name="view" href="https://test.com/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="https://test.com/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="https://test.com/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new ActionColumn(
                        urlCreator: static fn(
                            string $action,
                            DataContext $context
                        ): string => 'https://test.com/' . $action . '?id=' . $context->data['id'],
                    )
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testUrlQueryParameters(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?test-param=test.param&amp;id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?test-param=test.param&amp;id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?test-param=test.param&amp;id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?test-param=test.param&amp;id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?test-param=test.param&amp;id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?test-param=test.param&amp;id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new ActionColumn(
                        urlConfig: new UrlConfig(
                            queryParameters: ['test-param' => 'test.param'],
                            primaryKeyPlace: UrlConfig::QUERY_PARAMETERS,
                        ),
                    ),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testVisibleButtonsClosure(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            </td>
            </tr>
            <tr>
            <td>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new ActionColumn(
                        visibleButtons: [
                            'view' => static fn (array $data): bool => $data['id'] === 1,
                            'update' => static fn (array $data): bool => $data['id'] !== 1,
                        ],
                    ),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testObjectsWithPrimaryKey(): void
    {
        $dataReader = new IterableDataReader([
            new class () {
                public int $id = 23;
            },
            new class () {
                public int $id = 78;
            },
        ]);

        $html = GridView::widget()
            ->columns(new ActionColumn(urlConfig: new UrlConfig('id')))
            ->dataReader($dataReader)
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?id=23" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=23" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=23" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td>
            <a name="view" href="/admin/manage/view?id=78" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=78" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=78" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            $html
        );
    }

    public function testDefaultTemplateGeneration(): void
    {
        $this->initialize();

        $dataReader = new IterableDataReader([
            ['id' => 1],
            ['id' => 2],
        ]);

        $actionColumn = new ActionColumn(
            buttons: [
                'one' => static fn(string $url) => Html::a('1', $url)->render(),
                'two' => static fn(string $url) => Html::a('2', $url)->render(),
            ]
        );

        $html = GridView::widget()
            ->columns($actionColumn)
            ->dataReader($dataReader)
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a href="#">1</a>
            <a href="#">2</a>
            </td>
            </tr>
            <tr>
            <td>
            <a href="#">1</a>
            <a href="#">2</a>
            </td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            $html
        );
    }

    public function testDefaultTemplate(): void
    {
        $this->initialize();

        $dataReader = new IterableDataReader([
            ['id' => 1],
            ['id' => 2],
        ]);

        $actionColumn = new ActionColumn(
            buttons: [
                'one' => static fn(string $url) => Html::a('1', $url)->render(),
                'two' => static fn(string $url) => Html::a('2', $url)->render(),
            ]
        );

        $html = GridView::widget()
            ->columns($actionColumn)
            ->columnsConfigs([
                ActionColumn::class => [
                    'template' => '!{one}!',
                ],
            ])
            ->dataReader($dataReader)
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            !<a href="#">1</a>!
            </td>
            </tr>
            <tr>
            <td>
            !<a href="#">1</a>!
            </td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            $html
        );
    }

    public function testDefaultUrlCreator(): void
    {
        $this->initialize();

        $dataReader = new IterableDataReader([
            ['id' => 1],
            ['id' => 2],
        ]);

        $actionColumn = new ActionColumn(
            buttons: [
                'one' => static fn(string $url) => Html::a('1', $url)->render(),
            ]
        );

        $html = GridView::widget()
            ->columns($actionColumn)
            ->dataReader($dataReader)
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a href="#">1</a>
            </td>
            </tr>
            <tr>
            <td>
            <a href="#">1</a>
            </td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            $html
        );
    }

    public static function dataActionButtons(): array
    {
        return [
            'empty' => [
                '<a href="#"></a>',
                new ActionButton(),
            ],
            'content-string' => [
                '<a href="#">hello</a>',
                new ActionButton(content: 'hello'),
            ],
            'content-stringable' => [
                '<a href="#">hello</a>',
                new ActionButton(content: new StringableObject('hello')),
            ],
            'content-closure-data' => [
                '<a href="#">hello-1</a>',
                new ActionButton(
                    content: static fn(array $data) => new StringableObject('hello-' . $data['id']),
                ),
            ],
            'content-closure-context' => [
                '<a href="#">hello-0</a>',
                new ActionButton(
                    content: static fn(array $data, DataContext $context) => 'hello-' . $context->key,
                ),
            ],
            'url-string' => [
                '<a href="#1"></a>',
                new ActionButton(url: '#1'),
            ],
            'url-closure-data' => [
                '<a href="#id-1">test</a>',
                new ActionButton(
                    content: 'test',
                    url: static fn(array $data) => '#id-' . $data['id'],
                ),
            ],
            'url-closure-context' => [
                '<a href="#id-0">test</a>',
                new ActionButton(
                    content: 'test',
                    url: static fn(array $data, DataContext $context) => '#id-' . $context->key,
                ),
            ],
            'attributes-array' => [
                '<a href="#" data-test="hello">test</a>',
                new ActionButton(
                    content: 'test',
                    attributes: ['data-test' => 'hello']
                ),
            ],
            'attributes-closure' => [
                '<a href="#" data-t1="h1" data-t2="h0">test</a>',
                new ActionButton(
                    content: 'test',
                    attributes: static fn(array $data, DataContext $context) => [
                        'data-t1' => 'h' . $data['id'],
                        'data-t2' => 'h' . $context->key,
                    ]
                ),
            ],
            'class-string' => [
                '<a class="red" href="#"></a>',
                new ActionButton(class: 'red'),
            ],
            'class-closure' => [
                '<a class="h1 h0" href="#">test</a>',
                new ActionButton(
                    content: 'test',
                    class: static fn(array $data, DataContext $context) => [
                        'h' . $data['id'],
                        'h' . $context->key,
                    ]
                ),
            ],
            'add-class' => [
                '<a class="red green" href="#"></a>',
                new ActionButton(class: 'green'),
                ['buttonClass' => 'red'],
            ],
            'override-class' => [
                '<a class="green" href="#"></a>',
                new ActionButton(class: 'green', overrideAttributes: true),
                ['buttonClass' => 'red'],
            ],
            'add-attributes' => [
                '<a href="#" data-id="5" data-key="6"></a>',
                new ActionButton(attributes: ['data-key' => 6]),
                ['buttonAttributes' => ['data-id' => 5]],
            ],
            'override-attributes' => [
                '<a href="#" data-key="6"></a>',
                new ActionButton(attributes: ['data-key' => 6], overrideAttributes: true),
                ['buttonAttributes' => ['data-id' => 5]],
            ],
            'override-attributes-without-class' => [
                '<a class="red" href="#" data-key="6"></a>',
                new ActionButton(attributes: ['data-key' => 6], overrideAttributes: true),
                [
                    'buttonAttributes' => ['data-id' => 5],
                    'buttonClass' => 'red',
                ],
            ],
            'override-attributes-with-class' => [
                '<a class="green" href="#" data-key="6"></a>',
                new ActionButton(attributes: ['data-key' => 6], class: 'green', overrideAttributes: true),
                [
                    'buttonAttributes' => ['data-id' => 5],
                    'buttonClass' => 'red',
                ],
            ],
        ];
    }

    #[DataProvider('dataActionButtons')]
    public function testActionButtons(
        string $expected,
        ActionButton $button,
        array $columnConfig = [],
    ): void {
        $this->initialize();

        $dataReader = new IterableDataReader([
            ['id' => 1],
        ]);

        $actionColumn = new ActionColumn(
            buttons: [
                'button' => $button,
            ]
        );

        $html = GridView::widget()
            ->columns($actionColumn)
            ->columnsConfigs([
                ActionColumn::class => $columnConfig
            ])
            ->dataReader($dataReader)
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            $expected
            </td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            $html
        );
    }

    private function initialize(
        mixed $defaultUrlCreator = null,
    ): void {
        $currentRoute = new CurrentRoute();
        $currentRoute->setRouteWithArguments(Route::get('/admin/manage')->name('admin/manage'), []);

        $config = [
            CurrentRoute::class => $currentRoute,
            UrlGeneratorInterface::class => Mock::urlGenerator([], $currentRoute),
            ActionColumnRenderer::class => $rendererDefinition ?? [
                '__construct()' => [
                    'defaultUrlCreator' => $defaultUrlCreator,
                ],
            ],
        ];

        $container = new Container(ContainerConfig::create()->withDefinitions($config));
        WidgetFactory::initialize($container);
    }
}
