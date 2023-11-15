<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Html\Tag\A;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

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
            <table class="table">
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="actions"><a class="text-decoration-none" href="/admin/view?id=1" title="View">🔎</a></td>
            </tr>
            <tr>
            <td data-label="actions"><a class="text-decoration-none" href="/admin/view?id=2" title="View">🔎</a></td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    ActionColumn::create()
                        ->content(
                            /** @psalm-param string[] $data */
                            static fn (array $data): string => A::tag()
                                ->addAttributes(['class' => 'text-decoration-none', 'title' => 'View'])
                                ->content('🔎')
                                ->encode(false)
                                ->href('/admin/view?id=' . $data['id'])
                                ->render(),
                        ),
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
            <table class="table">
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td class="text-decoration-none test.class" data-label="actions"><a href="/admin/view?id=1" title="View">🔎</a></td>
            </tr>
            <tr>
            <td class="text-decoration-none test.class" data-label="actions"><a href="/admin/view?id=2" title="View">🔎</a></td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    ActionColumn::create()
                        ->content(
                            /** @psalm-param string[] $data */
                            static fn (array $data): string => A::tag()
                                ->addAttributes(['title' => 'View'])
                                ->content('🔎')
                                ->encode(false)
                                ->href('/admin/view?id=' . $data['id'])
                                ->render(),
                        )
                        ->contentAttributes(['class' => 'text-decoration-none test.class']),
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
    public function testCustomButton(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="actions">
            <a class="text-decoration-none" href="/admin/manage/resend-password?id=1" title="Resend password">&#128274;</a>
            </td>
            </tr>
            <tr>
            <td data-label="actions">
            <a class="text-decoration-none" href="/admin/manage/resend-password?id=2" title="Resend password">&#128274;</a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    ActionColumn::create()
                        ->buttons(
                            [
                                'resend-password' => static fn (string $url): string => A::tag()
                                    ->addAttributes(['class' => 'text-decoration-none', 'title' => 'Resend password'])
                                    ->content('&#128274;')
                                    ->encode(false)
                                    ->href($url)
                                    ->render(),
                            ],
                        )
                        ->template('{resend-password}')
                        ->visibleButtons(['resend-password' => true]),
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
    public function testDataLabel(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="test.label">
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="test.label">
            <a name="view" href="/admin/manage/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(ActionColumn::create()->dataLabel('test.label'))
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
    public function testFooterAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
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
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    ActionColumn::create()->footer('test.footer')->footerAttributes(['class' => 'test.class']),
                )
                ->footerEnabled(true)
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
    public function testLabel(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>test.label</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="test.label">
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="test.label">
            <a name="view" href="/admin/manage/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(ActionColumn::create()->label('test.label'))
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
            <table class="table">
            <thead>
            <tr>
            <th>Ενέργειες</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="ενέργειες">
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="ενέργειες">
            <a name="view" href="/admin/manage/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(ActionColumn::create()->label('Ενέργειες'))
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
            <table class="table">
            <thead>
            <tr>
            <th class="test.class">test.label</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="test.label">
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="test.label">
            <a name="view" href="/admin/manage/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(ActionColumn::create()->label('test.label')->labelAttributes(['class' => 'test.class']))
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
    public function testName(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td name="test.name" data-label="actions">
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td name="test.name" data-label="actions">
            <a name="view" href="/admin/manage/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(ActionColumn::create()->name('test.name'))
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
    public function testNotVisible(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr></tr>
            </thead>
            <tbody>
            <tr></tr>
            <tr></tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(ActionColumn::create()->visible(false))
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
    public function testPrimaryKey(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?identity_id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?identity_id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?identity_id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?identity_id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?identity_id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?identity_id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(ActionColumn::create()->primaryKey('identity_id'))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    ActionColumn::create(),
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
            <table class="table">
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?test-arguments=test.arguments&amp;id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?test-arguments=test.arguments&amp;id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?test-arguments=test.arguments&amp;id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?test-arguments=test.arguments&amp;id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?test-arguments=test.arguments&amp;id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?test-arguments=test.arguments&amp;id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(ActionColumn::create()->urlArguments(['test-arguments' => 'test.arguments']))
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
            <table class="table">
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="actions">
            <a name="view" href="https://test.com/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="https://test.com/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="https://test.com/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="actions">
            <a name="view" href="https://test.com/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="https://test.com/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="https://test.com/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    ActionColumn::create()
                    ->urlCreator(
                        /** @psalm-param string[] $data */
                        static fn (string $action, array $data): string => 'https://test.com/' . $action . '?id=' . $data['id'],
                    ),
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
    public function testUrlQueryParameters(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?test-param=test.param&amp;id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?test-param=test.param&amp;id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?test-param=test.param&amp;id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?test-param=test.param&amp;id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?test-param=test.param&amp;id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?test-param=test.param&amp;id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(ActionColumn::create()->urlQueryParameters(['test-param' => 'test.param']))
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
    public function testUrlParamsConfig(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?test-param=test.param&amp;id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?test-param=test.param&amp;id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?test-param=test.param&amp;id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?test-param=test.param&amp;id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?test-param=test.param&amp;id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?test-param=test.param&amp;id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(ActionColumn::create()->urlParamsConfig(['test-param' => 'test.param']))
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
    public function testVisibleButtonsClosure(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="actions">
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    ActionColumn::create()->visibleButtons(
                        [
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
}
