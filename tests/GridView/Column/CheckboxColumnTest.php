<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView\Column;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Di\Container;
use Yiisoft\Html\Tag\Input\Checkbox;
use Yiisoft\Yii\DataView\GridView\Column\CheckboxColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\GridView;

final class CheckboxColumnTest extends TestCase
{
    public function testBase(): void
    {
        $html = $this->createGridView([['id' => 1], ['id' => 2], ['id' => 3]])
            ->columns(new CheckboxColumn())
            ->render();

        $this->assertSame(
            <<<HTML
            <table>
            <thead>
            <tr>
            <th><input type="checkbox" name="checkbox-selection-all" value="1"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td><input type="checkbox" name="checkbox-selection" value="0"></td>
            </tr>
            <tr>
            <td><input type="checkbox" name="checkbox-selection" value="1"></td>
            </tr>
            <tr>
            <td><input type="checkbox" name="checkbox-selection" value="2"></td>
            </tr>
            </tbody>
            </table>
            HTML,
            $html,
        );
    }

    public function testHeader(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(new CheckboxColumn(header: 'Select Items'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th>Select Items</th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testMultipleFalse(): void
    {
        $html = $this->createGridView([['id' => 1], ['id' => 2]])
            ->columns(new CheckboxColumn(multiple: false))
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
            <td><input type="checkbox" name="checkbox-selection" value="0"></td>
            </tr>
            <tr>
            <td><input type="checkbox" name="checkbox-selection" value="1"></td>
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
            ->columns(new CheckboxColumn(name: 'selected_items'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td><input type="checkbox" name="selected_items" value="0"></td>
            HTML,
            $html,
        );
    }

    public function testInputAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new CheckboxColumn(
                    inputAttributes: ['class' => 'custom-checkbox', 'data-id' => 'test'],
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td><input type="checkbox" class="custom-checkbox" name="checkbox-selection" value="0" data-id="test"></td>
            HTML,
            $html,
        );
    }

    public function testContent(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(
                new CheckboxColumn(
                    content: static fn(Checkbox $checkbox, DataContext $context) => '<label>' . $checkbox . ' Row ' . $context->data['id'] . '</label>',
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td><label><input type="checkbox" name="checkbox-selection" value="0"> Row 1</label></td>
            HTML,
            $html,
        );
    }

    public function testHeaderAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(new CheckboxColumn(
                headerAttributes: ['class' => 'header-class'],
            ))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <th class="header-class"><input type="checkbox" name="checkbox-selection-all" value="1"></th>
            HTML,
            $html,
        );
    }

    public function testBodyAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(new CheckboxColumn(
                bodyAttributes: ['class' => 'body-class'],
            ))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td class="body-class"><input type="checkbox" name="checkbox-selection" value="0"></td>
            HTML,
            $html,
        );
    }

    public function testFooter(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(new CheckboxColumn(
                footer: 'Footer Content',
            ))
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

    public function testColumnAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(new CheckboxColumn(
                columnAttributes: ['class' => 'checkbox-col'],
            ))
            ->columnGrouping()
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <colgroup>
            <col class="checkbox-col">
            </colgroup>
            HTML,
            $html,
        );
    }

    public function testVisible(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->columns(new CheckboxColumn(visible: false))
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
            ->dataReader(new IterableDataReader($data));
    }
}
