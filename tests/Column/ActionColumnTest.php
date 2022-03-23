<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Tests\TestCase;

final class ActionColumnTest extends TestCase
{
    public function testActionColumnDefaultButtons(): void
    {
        $this->assertSame(['view', 'update', 'delete'], array_keys($this->actionColumn->getButtons()));
    }

    public function testActionColumnButtonCustom(): void
    {
        $gridView = $this->createGridView();
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $actionColumn = $this->actionColumn
            ->buttons(
                [
                    'admin/custom' => static fn ($url) => Html::a(
                        'custon',
                        $url,
                        ['class' => 'text-danger', 'title' => 'Custom'],
                    )->encode(false)->render(),
                ]
            )
            ->grid($gridView)
            ->template('{admin/custom}');

        $html = <<<'HTML'
        <td><a class="text-danger" href="/admin/custom/1" title="Custom">custon</a></td>
        HTML;

        $this->assertSame($html, $actionColumn->renderDataCell(['id' => 1], 1, 0));
    }

    public function testActionColumnButtonOptions(): void
    {
        $actionColumn = $this->actionColumn->buttonOptions(['disabled' => true]);

        $html = <<<'HTML'
        <td>
        <a href="/admin/view/1" disabled title="View" aria-label="View" data-name="view">
        <span>&#128065;</span>
        </a> <a href="/admin/update/1" disabled title="Update" aria-label="Update" data-name="update">
        <span>&#128393;</span>
        </a> <a href="/admin/delete/1" disabled title="Delete" aria-label="Delete" data-name="delete" data-confirm="Are you sure you want to delete this item?" data-method="post">
        <span>&#128465;</span>
        </a>
        </td>
        HTML;

        $this->assertEqualsHTML($html, $actionColumn->renderDataCell(['id' => 1], 1, 0));
    }

    public function testActionColumnOneButtonMatched(): void
    {
        $actionColumn = $this->actionColumn->template('{show} {edit} {delete}');
        $this->assertSame(['delete'], array_keys($actionColumn->getButtons()));
    }

    public function testActionColumnPrimaryKey(): void
    {
        $actionColumn = $this->actionColumn
            ->buttons(
                [
                    'admin/custom' => static fn ($url): string => Html::a($url)
                        ->attributes(['class' => 'text-danger', 'title' => 'Custom'])
                        ->encode(false)->render(),
                ]
            )
            ->template('{admin/custom}')
            ->primaryKey('user_id');

        $html = <<<'HTML'
        <td><a class="text-danger" title="Custom">/admin/custom/1</a></td>
        HTML;

        $this->assertSame($html, $actionColumn->renderDataCell(['user_id' => 1], 1, 0));

        $actionColumn->primaryName('test_id');

        $html = <<<'HTML'
        <td><a class="text-danger" title="Custom">/admin/custom?test_id=1</a></td>
        HTML;

        $this->assertSame($html, $actionColumn->renderDataCell(['user_id' => 1], 1, 0));
    }

    public function testActionColumnNoMatchedResults(): void
    {
        $actionColumn = $this->actionColumn->template('{show} {edit} {remove}');
        $this->assertEmpty($actionColumn->getButtons());
    }

    public function testActionColumnDashInButtonPlaceholder(): void
    {
        $actionColumn = $this->actionColumn->template('{show-items}');
        $this->assertEmpty($actionColumn->getButtons());
    }

    public function testActionColumnRenderDataCell(): void
    {
        $actionColumn = $this->actionColumn->urlCreator(static fn ($model, $key, $index) => 'http://test.com');
        $columnContents = $actionColumn->renderDataCell(['id' => 1], 1, 0);

        $viewButton = '<a href="http://test.com" title="View" aria-label="View" data-name="view"><span>&#128065;</span></a>';
        $updateButton = '<a href="http://test.com" title="Update" aria-label="Update" data-name="update"><span>&#128393;</span></a>';
        $deleteButton = '<a href="http://test.com" title="Delete" aria-label="Delete" data-name="delete" data-confirm="Are you sure you want to delete this item?" data-method="post"><span>&#128465;</span></a>';

        $html = "<td>$viewButton $updateButton $deleteButton</td>";
        $this->assertSame($html, $columnContents);

        $actionColumn = $this->actionColumn
            ->urlCreator(static fn ($model, $key, $index) => 'http://test.com')
            ->template('{update}')
            ->buttons(
                [
                    'update' => static fn ($url, $model, $key) => 'update_button',
                ]
            );

        //test default visible button
        $columnContents = $actionColumn->renderDataCell(['id' => 1], 1, 0);
        $this->assertSame('<td>update_button</td>', $columnContents);

        //test visible button
        $actionColumn->visibleButtons(['update' => true]);
        $columnContents = $actionColumn->renderDataCell(['id' => 1], 1, 0);
        $this->assertSame('<td>update_button</td>', $columnContents);

        //test visible button (condition is callback)
        $actionColumn->visibleButtons(['update' => static fn ($model, $key, $index) => $model['id'] === 1]);
        $columnContents = $actionColumn->renderDataCell(['id' => 1], 1, 0);
        $this->assertSame('<td>update_button</td>', $columnContents);

        //test invisible button
        $actionColumn->visibleButtons(['update' => false]);
        $columnContents = $actionColumn->renderDataCell(['id' => 1], 1, 0);
        $this->assertNotSame('<td>update_button</td>', $columnContents);

        //test invisible button (condition is callback)
        $actionColumn->visibleButtons(['update' => static fn ($model, $key, $index) => $model['id'] !== 1]);
        $columnContents = $this->actionColumn->renderDataCell(['id' => 1], 1, 0);
        $this->assertNotSame('<td>update_button</td>', $columnContents);
    }

    public function testWithHtmlA(): void
    {
        $actionColumn = $this->actionColumn
                ->buttons([
                    'admin/custom' => Html::a('Custom view'),
                ]);

        $html = <<<'HTML'
        <td><a href="/admin/custom/1">Custom view</a></td>
        HTML;

        $this->assertSame($html, $actionColumn->renderDataCell(['id' => 1], 1, 0));
    }

    public function testQueryParams(): void
    {
        $actionColumn = $this->actionColumn
                ->primaryParam()
                ->primaryName('user_id')
                ->queryParams([
                    'custom-param' => 1
                ])->buttons([
                    'admin/custom' => Html::a('Custom view'),
                ]);

        $html = <<<'HTML'
        <td><a href="/admin/custom?custom-param=1&amp;user_id=1">Custom view</a></td>
        HTML;

        $this->assertSame($html, $actionColumn->renderDataCell(['id' => 1], 1, 0));
    }
}
