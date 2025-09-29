<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView\Column;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Html\Tag\Input\Radio;
use Yiisoft\Yii\DataView\GridView\Column\RadioColumn;
use Yiisoft\Yii\DataView\GridView\Column\RadioColumnRenderer;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\GridView;

final class RadioColumnTest extends TestCase
{
    public function testBase(): void
    {
        $html = $this->createGridView([['id' => 1], ['id' => 2]])
            ->columns(new RadioColumn())
            ->render();

        $this->assertSame(
            <<<HTML
            <table>
            <thead>
            <tr>
            <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td><input type="radio" name="radio-selection" value="0"></td>
            </tr>
            <tr>
            <td><input type="radio" name="radio-selection" value="1"></td>
            </tr>
            </tbody>
            </table>
            HTML,
            $html,
        );
    }

    public function testName(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(new RadioColumn(name: 'selected_item'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td><input type="radio" name="selected_item" value="0"></td>
            HTML,
            $html,
        );
    }

    public function testInputAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new RadioColumn(
                    inputAttributes: ['class' => 'custom-radio', 'data-id' => 'test']
                )
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td><input type="radio" class="custom-radio" name="radio-selection" value="0" data-id="test"></td>
            HTML,
            $html,
        );
    }

    public function testContent(): void
    {
        $html = $this->createGridView([['id' => 1, 'name' => 'John']])
            ->columns(
                new RadioColumn(
                    content: static fn(Radio $radio, DataContext $context) => '<label>' . $radio . ' User ' . $context->data['name'] . '</label>'
                )
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td><label><input type="radio" name="radio-selection" value="0"> User John</label></td>
            HTML,
            $html,
        );
    }

    public function testHeader(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(new RadioColumn(header: 'Select One'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th>Select One</th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testHeaderAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new RadioColumn(
                    header: 'Select',
                    headerAttributes: ['class' => 'header-class']
                )
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <th class="header-class">Select</th>
            HTML,
            $html,
        );
    }

    public function testBodyAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new RadioColumn(
                    bodyAttributes: ['class' => 'body-class']
                )
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td class="body-class"><input type="radio" name="radio-selection" value="0"></td>
            HTML,
            $html,
        );
    }

    public function testColumnAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new RadioColumn(
                    columnAttributes: ['class' => 'radio-col']
                )
            )
            ->columnGrouping()
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <colgroup>
            <col class="radio-col">
            </colgroup>
            HTML,
            $html,
        );
    }

    public function testFooter(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new RadioColumn(
                    footer: 'Footer Content'
                ),
            )
            ->enableFooter()
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tfoot>
            <tr>
            <td>Footer Content</td>
            </tr>
            </tfoot>
            HTML,
            $html,
        );
    }

    public function testVisible(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(new RadioColumn(visible: false))
            ->render();

        $this->assertSame(
            <<<HTML
            <table>
            <thead>
            <tr></tr>
            </thead>
            <tbody>
            <tr></tr>
            </tbody>
            </table>
            HTML,
            $html,
        );
    }

    private function createGridView(array $data = []): GridView
    {
        return (new GridView(new Container()))
            ->layout('{items}')
            ->containerTag(null)
            ->dataReader(new IterableDataReader($data))
            ->addColumnRendererConfigs([
                RadioColumnRenderer::class => [],
            ]);
    }
}
