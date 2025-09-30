<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\ListView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\ListView\ListView;
use Yiisoft\Yii\DataView\ListView\ListItemContext;
use Yiisoft\Yii\DataView\Tests\Support\SimpleReadable;
use InvalidArgumentException;

final class ListViewTest extends TestCase
{
    public function testBase(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Eva'],
        ])
            ->itemView(static fn(array $data): string => $data['id'] . '. ' . $data['name'])
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
            <ul>
            <li>1. Anna</li>
            <li>2. Eva</li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            $html,
        );
    }

    public function testReadableOnly(): void
    {
        $html = $this->createListView(
            new SimpleReadable([
                ['id' => 1, 'name' => 'Anna'],
                ['id' => 2, 'name' => 'Eva'],
            ])
        )
            ->itemView(static fn(array $data): string => $data['id'] . '. ' . $data['name'])
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
            <ul>
            <li>1. Anna</li>
            <li>2. Eva</li>
            </ul>
            </div>
            HTML,
            $html,
        );
    }

    public function testListTag(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->listTag('div')
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <div>
            <li>Anna</li>
            <li>Bob</li>
            </div>
            HTML,
            $html,
        );
    }

    public function testListTagWithEmptyString(): void
    {
        $widget = new ListView();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->listTag('');
    }

    public function testListAttributes(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
        ])
            ->listAttributes(['class' => 'custom-list', 'id' => 'my-list'])
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            '<ul id="my-list" class="custom-list">',
            $html,
        );
    }

    public function testItemTag(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->itemTag('div')
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <ul>
            <div>Anna</div>
            <div>Bob</div>
            </ul>
            HTML,
            $html,
        );
    }

    public function testItemTagWithEmptyString(): void
    {
        $widget = new ListView();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->itemTag('');
    }

    public function testItemAttributes(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->itemAttributes(['class' => 'list-item', 'data-test' => 'item'])
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <ul>
            <li class="list-item" data-test="item">Anna</li>
            <li class="list-item" data-test="item">Bob</li>
            </ul>
            HTML,
            $html,
        );
    }

    public function testBeforeItem(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->beforeItem('<!-- before item -->')
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <!-- before item --><li>Anna</li>
            <!-- before item --><li>Bob</li>
            HTML,
            $html,
        );
    }

    public function testAfterItem(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->afterItem('<!-- after item -->')
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <li>Anna</li><!-- after item -->
            <li>Bob</li><!-- after item -->
            HTML,
            $html,
        );
    }

    public function testSeparator(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->separator('<li>---</li>')
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <ul>
            <li>Anna</li><li>---</li><li>Bob</li>
            </ul>
            HTML,
            $html,
        );
    }

    public function testNoResultsTag(): void
    {
        $html = $this->createListView()
            ->noResultsTag('b')
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            '<b>No results found.</b>',
            $html,
        );
    }

    public function testNoResultsTagWithEmptyString(): void
    {
        $widget = new ListView();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->noResultsTag('');
    }

    public function testNoResultsAttributes(): void
    {
        $html = $this->createListView()
            ->noResultsAttributes(['class' => 'empty-state', 'data-test' => 'empty'])
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            '<p class="empty-state" data-test="empty">No results found.</p>',
            $html,
        );
    }

    private function createListView(ReadableDataInterface|array $data = []): ListView
    {
        $dataReader = $data instanceof ReadableDataInterface ? $data : new IterableDataReader($data);

        return (new ListView())->dataReader($dataReader);
    }
}
