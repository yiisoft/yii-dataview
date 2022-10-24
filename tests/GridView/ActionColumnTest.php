<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

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
use Yiisoft\Yii\DataView\Tests\Support\Mock;
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
                ->columns($this->createColumnsWithContent())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithContentAttributes())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithButtonCustom())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithDataLabel())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithFooterAttributes())
                ->footerEnabled(true)
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithLabel())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithLabelMbString())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithLabelAttributes())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithName())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithNotVisible())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithPrimaryKey())
                ->id('w1-grid')
                ->paginator(
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
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithUrlArguments())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithUrlCreator())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithUrlQueryParameters())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithUrlParamsConfig())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
                ->columns($this->createColumnsWithVisibleButtonsClosure())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    /**
     * @psalm-return array<ActionColumn|DataColumn>
     */
    private function createColumns(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            ActionColumn::create(),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithButtonCustom(): array
    {
        return [
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
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithContent(): array
    {
        return [
            ActionColumn::create()
                ->content(
                    static fn (array $data): string => A::tag()
                        ->addAttributes(['class' => 'text-decoration-none', 'title' => 'View'])
                        ->content('🔎')
                        ->encode(false)
                        ->href('/admin/view?id=' . $data['id'])
                        ->render(),
                ),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithContentAttributes(): array
    {
        return [
            ActionColumn::create()
                ->content(
                    static fn (array $data): string => A::tag()
                        ->addAttributes(['title' => 'View'])
                        ->content('🔎')
                        ->encode(false)
                        ->href('/admin/view?id=' . $data['id'])
                        ->render(),
                )
                ->contentAttributes(['class' => 'text-decoration-none test.class']),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithDataLabel(): array
    {
        return [
            ActionColumn::create()->dataLabel('test.label'),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithFooterAttributes(): array
    {
        return [
            ActionColumn::create()
                ->footer('test.footer')
                ->footerAttributes(['class' => 'test.class']),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithLabel(): array
    {
        return [
            ActionColumn::create()->label('test.label'),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithLabelMbString(): array
    {
        return [
            ActionColumn::create()->label('Ενέργειες'),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithLabelAttributes(): array
    {
        return [
            ActionColumn::create()->label('test.label')->labelAttributes(['class' => 'test.class']),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithName(): array
    {
        return [
            ActionColumn::create()->name('test.name'),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithNotVisible(): array
    {
        return [
            ActionColumn::create()->visible(false),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithPrimaryKey(): array
    {
        return [
            ActionColumn::create()->primaryKey('identity_id'),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithUrlArguments(): array
    {
        return [
            ActionColumn::create()->urlArguments(['test-arguments' => 'test.arguments']),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithUrlCreator(): array
    {
        return [
            ActionColumn::create()
                ->urlCreator(
                    static fn (string $action, array $data): string => 'https://test.com/' . $action . '?id=' . $data['id'],
                ),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithUrlQueryParameters(): array
    {
        return [
            ActionColumn::create()
                ->urlQueryParameters(['test-param' => 'test.param']),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithUrlParamsConfig(): array
    {
        return [
            ActionColumn::create()->urlParamsConfig(['test-param' => 'test.param']),
        ];
    }

    /**
     * @psalm-return array<ActionColumn>
     */
    private function createColumnsWithVisibleButtonsClosure(): array
    {
        return [
            ActionColumn::create()->visibleButtons(
                [
                    'view' => static fn (array $data): bool => $data['id'] === 1,
                    'update' => static fn (array $data): bool => $data['id'] !== 1,
                ],
            ),
        ];
    }
}
