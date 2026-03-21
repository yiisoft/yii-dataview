<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView\Column;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Di\Container;
use Yiisoft\Yii\DataView\GridView\Column\ActionButton;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumnRenderer;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\SimpleActionColumnUrlCreator;
use Yiisoft\Yii\DataView\Tests\Support\StringableObject;

final class ActionColumnTest extends TestCase
{
    public function testBase(): void
    {
        $html = $this->createGridView([['id' => 1], ['id' => 2]])
            ->columns(new ActionColumn())
            ->render();

        $this->assertSame(
            <<<HTML
            <table>
            <thead>
            <tr>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>
            <a title="View" href="/view/1">🔎</a>
            <a title="Update" href="/update/1">✎</a>
            <a title="Delete" href="/delete/1">❌</a>
            </td>
            </tr>
            <tr>
            <td>
            <a title="View" href="/view/2">🔎</a>
            <a title="Update" href="/update/2">✎</a>
            <a title="Delete" href="/delete/2">❌</a>
            </td>
            </tr>
            </tbody>
            </table>
            HTML,
            $html,
        );
    }

    public function testTemplate(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(new ActionColumn(template: '{view} / {delete}'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>
            <a title="View" href="/view/1">🔎</a> / <a title="Delete" href="/delete/1">❌</a>
            </td>
            HTML,
            $html,
        );
    }

    public function testTemplateInRenderer(): void
    {
        $html = $this->createGridView([['id' => 1]], ['template' => '{view} / {delete}'])
            ->columns(new ActionColumn())
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>
            <a title="View" href="/view/1">🔎</a> / <a title="Delete" href="/delete/1">❌</a>
            </td>
            HTML,
            $html,
        );
    }

    public function testBeforeAfter(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new ActionColumn(
                    before: '<span class="bold">',
                    after: '</span>',
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>
            <span class="bold"><a title="View" href="/view/1">🔎</a>
            <a title="Update" href="/update/1">✎</a>
            <a title="Delete" href="/delete/1">❌</a></span>
            </td>
            HTML,
            $html,
        );
    }

    public function testBeforeAfterColumnTakesPriorityOverRenderer(): void
    {
        $html = $this->createGridView(
            [['id' => 1]],
            ['before' => '<div class="renderer">', 'after' => '</div>'],
        )
            ->columns(
                new ActionColumn(
                    content: 'buttons',
                    before: '<span class="column">',
                    after: '</span>',
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>
            <span class="column">buttons</span>
            </td>
            HTML,
            $html,
        );
        $this->assertStringNotContainsString('renderer', $html);
    }

    public function testBeforeAfterFallbackToRenderer(): void
    {
        $html = $this->createGridView(
            [['id' => 1]],
            ['before' => '<div class="renderer">', 'after' => '</div>'],
        )
            ->columns(
                new ActionColumn(content: 'buttons'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>
            <div class="renderer">buttons</div>
            </td>
            HTML,
            $html,
        );
    }

    public function testUrlConfig(): void
    {
        $html = $this->createGridView([['id' => 1, 'slug' => 'item1']])
            ->columns(
                new ActionColumn(
                    template: '{view}',
                    urlConfig: ['primaryKey' => 'slug'],
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>
            <a title="View" href="/view/item1">🔎</a>
            </td>
            HTML,
            $html,
        );
    }

    public function testUrlCreator(): void
    {
        $html = $this->createGridView([['id' => 1, 'slug' => 'item1']])
            ->columns(
                new ActionColumn(
                    template: '{view}',
                    urlCreator: new SimpleActionColumnUrlCreator(primaryKey: 'slug'),
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>
            <a title="View" href="/view/item1">🔎</a>
            </td>
            HTML,
            $html,
        );
    }

    public function testHeaderFooter(): void
    {
        $html = $this->createGridView()
            ->columns(
                new ActionColumn(
                    header: 'ACTIONS UP',
                    footer: 'ACTIONS DOWN',
                ),
            )
            ->enableFooter()
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th>ACTIONS UP</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
            <td>ACTIONS DOWN</td>
            </tr>
            </tfoot>
            HTML,
            $html,
        );
    }

    public static function dataContent(): iterable
    {
        yield 'string' => ['Custom Content', 'Custom Content'];
        yield 'stringable' => ['Custom Content', new StringableObject('Custom Content')];
        yield 'callable' => [
            '"1"',
            static fn(array $data, DataContext $context) => '"' . $data['id'] . '"',
        ];
        yield 'integer' => ['42', 42];
        yield 'callable-returning-integer' => [
            '1',
            static fn(array $data, DataContext $context) => $data['id'],
        ];
    }

    #[DataProvider('dataContent')]
    public function testContent(string $expected, mixed $content): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(new ActionColumn(content: $content))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>
            $expected
            </td>
            HTML,
            $html,
        );
    }

    public function testButtons(): void
    {
        $html = $this->createGridView([['id' => 1, 'slug' => 'item1']])
            ->columns(
                new ActionColumn(
                    buttons: [
                        'view' => new ActionButton('V', '#view'),
                        'send' => new ActionButton('S', '#send', title: 'Send to…'),
                        'edit' => static fn(string $url) => '<a href="' . $url . '">EDIT</a>',
                        'delete' => new ActionButton(
                            content: static fn(array $data, DataContext $context) => 'Del ' . $data['id'],
                            url: static fn(array $data, DataContext $context) => '/confirm-delete/' . $data['id'],
                            attributes: static fn(array $data, DataContext $context) => ['data-id' => 'id-' . $data['id']],
                            class: static fn(array $data, DataContext $context) => 'red' . $data['id'],
                        ),
                    ],
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>
            <a href="#view">V</a>
            <a title="Send to…" href="#send">S</a>
            <a href="/edit/1">EDIT</a>
            <a data-id="id-1" class="red1" href="/confirm-delete/1">Del 1</a>
            </td>
            HTML,
            $html,
        );
    }

    #[TestWith(['<a title="View" href="#">V</a>', true])]
    #[TestWith(['<a class="red" title="View" href="#">V</a>', false])]
    public function testButtonOverrideAttribute(string $expected, bool $override): void
    {
        $html = $this->createGridView(
            [['id' => 1, 'slug' => 'item1']],
            ['buttonAttributes' => ['class' => 'red']],
        )
            ->columns(
                new ActionColumn(
                    buttons: [
                        'view' => new ActionButton(
                            'V',
                            '#',
                            attributes: ['title' => 'View'],
                            overrideAttributes: $override,
                        ),
                    ],
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>
            $expected
            </td>
            HTML,
            $html,
        );
    }

    public function testButtonClassAppliedWhenButtonClassIsFalse(): void
    {
        $html = $this->createGridView(
            [['id' => 1]],
            ['buttonClass' => 'btn'],
        )
            ->columns(
                new ActionColumn(
                    buttons: [
                        'view' => new ActionButton('V', '#'),
                    ],
                ),
            )
            ->render();

        $this->assertStringContainsString(
            '<a class="btn" href="#">V</a>',
            $html,
        );
    }

    public function testButtonClassNotAppliedWhenOverrideAttributes(): void
    {
        $html = $this->createGridView(
            [['id' => 1]],
            ['buttonClass' => 'btn'],
        )
            ->columns(
                new ActionColumn(
                    buttons: [
                        'view' => new ActionButton(
                            'V',
                            '#',
                            class: 'custom-class',
                            overrideAttributes: true,
                        ),
                    ],
                ),
            )
            ->render();

        $this->assertStringContainsString(
            '<a class="custom-class" href="#">V</a>',
            $html,
        );
        $this->assertStringNotContainsString('btn', $html);
    }

    public function testButtonClassMergedWhenNotOverrideAttributes(): void
    {
        $html = $this->createGridView(
            [['id' => 1]],
            ['buttonClass' => 'btn'],
        )
            ->columns(
                new ActionColumn(
                    buttons: [
                        'view' => new ActionButton(
                            'V',
                            '#',
                            class: 'custom-class',
                            overrideAttributes: false,
                        ),
                    ],
                ),
            )
            ->render();

        $this->assertStringContainsString(
            '<a class="btn custom-class" href="#">V</a>',
            $html,
        );
    }

    public function testVisibleButtonsUnlistedAreHidden(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new ActionColumn(
                    visibleButtons: [
                        'view' => true,
                    ],
                ),
            )
            ->render();

        $this->assertStringContainsString('<a title="View" href="/view/1">', $html);
        $this->assertStringNotContainsString('Update', $html);
        $this->assertStringNotContainsString('Delete', $html);
    }

    public function testVisibleButtons(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new ActionColumn(
                    visibleButtons: [
                        'view' => true,
                        'update' => false,
                        'delete' => true,
                    ],
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>
            <a title="View" href="/view/1">🔎</a>

            <a title="Delete" href="/delete/1">❌</a>
            </td>
            HTML,
            $html,
        );
    }

    public function testVisibleButtonsCallable(): void
    {
        $html = $this->createGridView([
            'a' => ['id' => 1],
            'b' => ['id' => 2],
        ])
            ->columns(
                new ActionColumn(
                    visibleButtons: [
                        'view' => static fn(array $data, mixed $key, int $index) => $index === 0,
                        'update' => static fn(array $data, mixed $key, int $index) => $key === 'b',
                        'delete' => static fn(array $data, mixed $key, int $index) => $key === 'a',
                    ],
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            <tr>
            <td>
            <a title="View" href="/view/1">🔎</a>

            <a title="Delete" href="/delete/1">❌</a>
            </td>
            </tr>
            <tr>
            <td>
            <a title="Update" href="/update/2">✎</a>
            </td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    public function testColumnAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new ActionColumn(
                    columnAttributes: ['class' => 'red'],
                ),
            )
            ->columnGrouping()
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <colgroup>
            <col class="red">
            </colgroup>
            HTML,
            $html,
        );
    }

    public function testHeaderAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new ActionColumn(
                    headerAttributes: ['class' => 'red'],
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th class="red">Actions</th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testBodyAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new ActionColumn(
                    content: 'buttons',
                    bodyAttributes: ['class' => 'red'],
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td class="red">
            buttons
            </td>
            HTML,
            $html,
        );
    }

    public function testFooterAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new ActionColumn(
                    footerAttributes: ['class' => 'red'],
                ),
            )
            ->enableFooter()
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tfoot>
            <tr>
            <td class="red">&nbsp;</td>
            </tr>
            </tfoot>
            HTML,
            $html,
        );
    }

    public function testActionButtonDefaultOverrideAttributes(): void
    {
        $button = new ActionButton();

        $this->assertFalse($button->overrideAttributes);
    }

    public function testVisible(): void
    {
        $html = $this->createGridView([['id' => 1], ['id' => 2]])
            ->columns(
                new ActionColumn(visible: false),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <table>
            <thead>
            <tr></tr>
            </thead>
            <tbody>
            <tr></tr>
            <tr></tr>
            </tbody>
            </table>
            HTML,
            $html,
        );
    }

    private function createGridView(array $data = [], array $rendererConfig = []): GridView
    {
        return (new GridView(new Container()))
            ->layout('{items}')
            ->containerTag(null)
            ->dataReader(new IterableDataReader($data))
            ->addColumnRendererConfigs([
                ActionColumnRenderer::class => array_merge(
                    [
                        'urlCreator' => new SimpleActionColumnUrlCreator(),
                    ],
                    $rendererConfig,
                ),
            ]);
    }
}
