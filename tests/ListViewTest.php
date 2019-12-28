<?php

namespace Yiisoft\Yii\DataView\Tests;

use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Yii\DataView\ListView;

/**
 * @group widgets
 */
class ListViewTest extends TestCase
{
    public function testEmptyListShown(): void
    {
        $dataReader = $this->createDataReader([]);
        $listView = $this->getListView($dataReader, new OffsetPaginator($dataReader));
        $listView->withEmptyText('Nothing at all');
        $out = $listView->run();
        $this->assertEquals('<div id="w0" class="list-view"><div class="empty">Nothing at all</div></div>', $out);
    }

    public function testEmptyListNotShown(): void
    {
        $listView = $this->getListView($this->createDataReader([]), null);
        $out = $listView->run();

        $this->assertEquals(
            '<div id="w0" class="list-view"><div class="empty">No results found.</div></div>'
            ,
            $out
        );
    }

    public function testEmpty(): void
    {
        $out = $this->getListView($this->createDataReader([]), null)
            ->showEmptyText(false)
            ->run();

        $this->assertEquals('<div id="w0" class="list-view"></div>', $out);
    }

    public function testSimplyListView(): void
    {
        $dataReader = $this->createDataReader([0, 1, 2]);
        $listView = $this->getListView($dataReader, null);

        $out = $listView->run();

        $this->assertEquals(
            <<<'HTML'
<div id="w0" class="list-view"><div class="summary">Total <b>{count, number}</b> {count, plural, one{item} other{items}}.</div>
<div data-key="0">0</div>
<div data-key="1">1</div>
<div data-key="2">2</div>
</div>
HTML
            ,
            $out
        );
    }

    public function testWidgetOptions(): void
    {
        $dataReader = $this->createDataReader([0, 1, 2]);
        $listView = $this->getListView($dataReader, null);
        $listView->separator = '';
        $listView->withOptions(['class' => 'test-passed']);
        $out = $listView->run();

        $this->assertEquals(
            <<<'HTML'
<div id="w0" class="test-passed"><div class="summary">Total <b>{count, number}</b> {count, plural, one{item} other{items}}.</div>
<div data-key="0">0</div><div data-key="1">1</div><div data-key="2">2</div>
</div>
HTML
            ,
            $out
        );
    }

    public function itemViewOptions(): array
    {
        return [
            [
                null,
                '<div id="w0" class="list-view"><div class="summary">Total <b>{count, number}</b> {count, plural, one{item} other{items}}.</div>
<div data-key="0">0</div>
<div data-key="1">1</div>
<div data-key="2">2</div>
</div>',
            ],
            [
                static function ($model, $key, $index, $widget) {
                    return "Item #{$index}: {$model['login']} - Widget: " . get_class($widget);
                },
                '<div id="w0" class="list-view"><div class="summary">Total <b>{count, number}</b> {count, plural, one{item} other{items}}.</div>
<div data-key="0">Item #0: silverfire - Widget: Yiisoft\Yii\DataView\ListView</div>
<div data-key="1">Item #1: samdark - Widget: Yiisoft\Yii\DataView\ListView</div>
<div data-key="2">Item #2: cebe - Widget: Yiisoft\Yii\DataView\ListView</div>
</div>',
            ],
            [
                '@view/widgets/ListView/item.php',
                '<div id="w0" class="list-view"><div class="summary">Total <b>{count, number}</b> {count, plural, one{item} other{items}}.</div>
<div data-key="0">Item #0: silverfire - Widget: Yiisoft\Yii\DataView\ListView</div>
<div data-key="1">Item #1: samdark - Widget: Yiisoft\Yii\DataView\ListView</div>
<div data-key="2">Item #2: cebe - Widget: Yiisoft\Yii\DataView\ListView</div>
</div>',
            ],
        ];
    }

    /**
     * @dataProvider itemViewOptions
     * @param mixed $itemView
     * @param string $expected
     */
    public function testItemViewOptions($itemView, $expected): void
    {
        $dataReader = $this->createDataReader(
            [
                ['login' => 'silverfire'],
                ['login' => 'samdark'],
                ['login' => 'cebe'],
            ]
        );
        $listView = $this->getListView($dataReader, null);
        $listView->itemView = $itemView;
        $out = $listView->run();
        $this->assertEquals($expected, $out);
    }

    public function itemOptions(): array
    {
        return [
            [
                [],
                '<div id="w0" class="list-view"><div class="summary">Total <b>{count, number}</b> {count, plural, one{item} other{items}}.</div>
<div data-key="0">0</div>
<div data-key="1">1</div>
<div data-key="2">2</div>
</div>',
            ],
            [
                static function ($model, $key, $index) {
                    return [
                        'tag' => 'span',
                        'data' => [
                            'test' => 'passed',
                            'key' => $key,
                            'index' => $index,
                            'id' => $model['id'],
                        ],
                    ];
                },
                // TODO fix test case
                '<div id="w0" class="list-view"><div class="summary">Total <b>{count, number}</b> {count, plural, one{item} other{items}}.</div>
<span data-test="passed" data-key="0" data-index="0" data-id="1" data-key="0">0</span>
<span data-test="passed" data-key="1" data-index="1" data-id="2" data-key="1">1</span>
<span data-test="passed" data-key="2" data-index="2" data-id="3" data-key="2">2</span>
</div>',
            ],
        ];
    }

    /**
     * @dataProvider itemOptions
     * @param mixed $itemOptions
     * @param string $expected
     */
    public function testItemOptions($itemOptions, $expected): void
    {
        $dataReader = $this->createDataReader(
            [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ]
        );
        $out = $this->getListView($dataReader, null)
            ->withItemOptions($itemOptions)
            ->run();

        $this->assertEquals($expected, $out);
    }

    public function testBeforeAndAfterItem(): void
    {
        $before = static function ($model, $key, $index, $widget) {
            $widget = get_class($widget);

            return "<!-- before: {$model['id']}, key: $key, index: $index, widget: $widget -->";
        };
        $after = function ($model, $key, $index, $widget) {
            if ($model['id'] === 1) {
                return;
            }
            $widget = get_class($widget);

            return "<!-- after: {$model['id']}, key: $key, index: $index, widget: $widget -->";
        };

        $dataReader = $this->createDataReader(
            [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ]
        );
        $listView = $this->getListView($dataReader, null);
        $listView->beforeItem = $before;
        $listView->afterItem = $after;

        $out = $listView->run();

        $this->assertEquals(
            <<<HTML
<div id="w0" class="list-view"><div class="summary">Total <b>{count, number}</b> {count, plural, one{item} other{items}}.</div>
<!-- before: 1, key: 0, index: 0, widget: Yiisoft\Yii\DataView\ListView -->
<div data-key="0">0</div>
<!-- before: 2, key: 1, index: 1, widget: Yiisoft\Yii\DataView\ListView -->
<div data-key="1">1</div>
<!-- after: 2, key: 1, index: 1, widget: Yiisoft\Yii\DataView\ListView -->
<!-- before: 3, key: 2, index: 2, widget: Yiisoft\Yii\DataView\ListView -->
<div data-key="2">2</div>
<!-- after: 3, key: 2, index: 2, widget: Yiisoft\Yii\DataView\ListView -->
</div>
HTML
            ,
            $out
        );
    }

    /**
     * @see https://github.com/yiisoft/yii2/pull/14596
     */
    public function testShouldTriggerInitEvent(): void
    {
        $this->markTestIncomplete();
        $dataReader = $this->createDataReader([0, 1, 2]);
        $initTriggered = false;

        $this->getListView(
            [
                'dataReader' => $dataReader,
                'on widget.init' => function () use (&$initTriggered) {
                    $initTriggered = true;
                },
            ]
        );
        $this->assertTrue($initTriggered);
    }

    private function createDataReader(array $models): IterableDataReader
    {
        return new IterableDataReader($models);
    }

    /**
     * @param $dataReader
     * @param $paginator
     * @return ListView
     */
    private function getListView($dataReader, $paginator): ListView
    {
        return ListView::widget()
            ->withOptions(['id' => 'w0', 'class' => 'list-view'])
            ->withDataReader($dataReader)
            ->withPaginator($paginator);
    }
}
